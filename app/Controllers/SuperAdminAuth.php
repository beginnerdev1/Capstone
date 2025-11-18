<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SuperAdminModel;
use App\Models\AdminModel;
use App\Models\AdminActivityLogModel;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;

class SuperAdminAuth extends Controller
{
    public function loginForm()
    {
        // Show login form. Flow: login (email+password) -> check-code -> complete login
        return view('superadmin/login');
    }

    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $SuperAdminModel = new SuperAdminModel();
        $super_admin = $SuperAdminModel->where('email', $email)->first();

        // Verify credentials first. Complete login only after one-time OTP entry.
        if ($super_admin && password_verify($password, $super_admin['password'])) {
            // Generate a cryptographically secure numeric OTP and store a hash in otp_hash
            try {
                $plainOtp = $SuperAdminModel->generateLoginOtp();
                $hashed = password_hash($plainOtp, PASSWORD_DEFAULT);
                $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                // reset attempt counters
                $SuperAdminModel->update($super_admin['id'], [
                    'otp_hash' => $hashed,
                    'otp_expires' => $expires,
                    'otp_failed_attempts' => 0,
                    'otp_locked_until' => null,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'SuperAdmin login: failed to generate/store OTP: ' . $e->getMessage());
                $plainOtp = null;
            }

            // Send the one-time OTP to the superadmin's email. Use Brevo transactional API if available.
            if (!empty($super_admin['email']) && !empty($plainOtp)) {
                try {
                    require ROOTPATH . 'vendor/autoload.php';
                    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('BREVO_API_KEY'));
                    $api = new TransactionalEmailsApi(new GuzzleClient(), $config);
                    $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                    $payload = new SendSmtpEmail([
                        'subject' => 'Your Super Admin Login OTP',
                        'sender' => ['name' => 'Super Admin Access', 'email' => $fromEmail],
                        'to' => [[ 'email' => $super_admin['email'] ]],
                        'htmlContent' => '<p>Your one-time login code is: <b>' . esc($plainOtp) . '</b>. It expires in 10 minutes.</p>'
                    ]);
                    $api->sendTransacEmail($payload);
                } catch (\Throwable $e) {
                    log_message('error', 'Failed sending superadmin OTP email: ' . $e->getMessage());
                }
            }

            // Store pending id for check-code step (OTP)
            session()->set('pending_superadmin_id', $super_admin['id']);
            session()->set('pending_superadmin_email', $super_admin['email']);
            return redirect()->to('/superadmin/check-code');
        }

        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function logout()
    {
        try {
            $logId = session()->get('superadmin_activity_log_id');
            if ($logId) {
                $logModel = new AdminActivityLogModel();
                $logModel->update($logId, ['logged_out_at' => date('Y-m-d H:i:s')]);
                session()->remove('superadmin_activity_log_id');
            }
        } catch (\Throwable $e) { }
        session()->destroy();
        return redirect()->to('/superadmin/login');
    }

    public function forgotPassword()
    {
        return view('superadmin/forgot_password');
    }

    public function createAccount()
    {
        $superAdminModel = new SuperAdminModel();
        $newAdminCode    = $superAdminModel->generateAdminCode();

        $data = [
            'admin_code' => $newAdminCode,
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        $superAdminModel->insert($data);

        return redirect()->back()->with('success', 'SuperAdmin created successfully!');
    }

    public function checkCodeForm()
    {
        return view('superadmin/check_code');
    }

    public function checkCode()
    {
        $code = $this->request->getPost('admin_code');
        $superAdminModel = new SuperAdminModel();

        $pendingId = session()->get('pending_superadmin_id');
        if ($pendingId) {
            $row = $superAdminModel->find($pendingId);
            if (!$row) {
                return redirect()->back()->with('error', 'Account not found.');
            }

            // Check for lockout
            $lockedUntil = isset($row['otp_locked_until']) && $row['otp_locked_until'] ? strtotime($row['otp_locked_until']) : 0;
            if ($lockedUntil > time()) {
                $wait = $lockedUntil - time();
                return redirect()->back()->with('error', 'Too many failed attempts. Try again in ' . $wait . ' seconds.');
            }

            $otpHash = $row['otp_hash'] ?? null;
            $otpExpires = !empty($row['otp_expires']) ? strtotime($row['otp_expires']) : 0;

            // Verify OTP if present
            if ($otpHash && $otpExpires > time() && password_verify((string)$code, $otpHash)) {
                // clear otp fields
                try {
                    $superAdminModel->update($pendingId, [
                        'otp_hash' => null,
                        'otp_expires' => null,
                        'otp_failed_attempts' => 0,
                        'otp_locked_until' => null,
                    ]);
                } catch (\Throwable $_) { }

                // Complete login
                $sessData = [
                    'superadmin_id' => $row['id'],
                    'superadmin_email' => $row['email'],
                    'is_superadmin_logged_in' => true,
                ];
                if (isset($row['first_name']))  $sessData['superadmin_first_name']  = $row['first_name'];
                if (isset($row['middle_name'])) $sessData['superadmin_middle_name'] = $row['middle_name'];
                if (isset($row['last_name']))   $sessData['superadmin_last_name']   = $row['last_name'];
                session()->set($sessData);
                try { session()->regenerate(true); } catch (\Throwable $e) { }

                session()->remove('pending_superadmin_id');
                session()->remove('pending_superadmin_email');

                // Log login
                try {
                    $logModel = new AdminActivityLogModel();
                    $resource = $this->request->getVar('file') ?? $this->request->getVar('path') ?? basename($this->request->getUri()->getPath());
                    $logId = $logModel->insert([
                        'actor_type' => 'superadmin',
                        'actor_id'   => $row['id'],
                        'action'     => 'login',
                        'route'      => '/superadmin/login',
                        'resource'   => $resource,
                        'method'     => 'POST',
                        'ip_address' => $this->request->getIPAddress(),
                        'user_agent' => substr((string)($this->request->getUserAgent() ?? ''), 0, 255),
                    ], true);
                    session()->set('superadmin_activity_log_id', $logId);
                } catch (\Throwable $e) { }

                return redirect()->to('/superadmin');
            }

            // OTP failed or expired — increment failure counter and possibly lock
            try {
                $fails = (int) ($row['otp_failed_attempts'] ?? 0);
                $fails++;
                $update = ['otp_failed_attempts' => $fails];
                if ($fails >= 5) {
                    $update['otp_locked_until'] = date('Y-m-d H:i:s', time() + 300); // 5 minute lock
                }
                $superAdminModel->update($pendingId, $update);
            } catch (\Throwable $_) { }

            return redirect()->back()->with('error', 'Invalid or expired login code.');
        }

        // Fallback: allow entering admin_code first (legacy)
        $super_admin = $superAdminModel->where('admin_code', $code)->first();
        if ($super_admin) {
            $sessData = [
                'superadmin_code_verified' => true,
                'superadmin_id'   => $super_admin['id'],
                'superadmin_code' => $super_admin['admin_code'],
            ];
            if (isset($super_admin['first_name']))  $sessData['superadmin_first_name']  = $super_admin['first_name'];
            if (isset($super_admin['middle_name'])) $sessData['superadmin_middle_name'] = $super_admin['middle_name'];
            if (isset($super_admin['last_name']))   $sessData['superadmin_last_name']   = $super_admin['last_name'];
            session()->set($sessData);
            try { session()->regenerate(true); } catch (\Throwable $e) { }
            return redirect()->to('/superadmin/login');
        }

        return redirect()->back()->with('error', 'Invalid Admin Code');
    }
}
