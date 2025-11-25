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

                // Generate and rotate admin_code immediately so we can email it with the OTP.
                $newAdminCode = $SuperAdminModel->generateAdminCode();
                $hashedAdminCode = password_hash($newAdminCode, PASSWORD_DEFAULT);

                // reset attempt counters and save otp + new hashed admin_code
                $SuperAdminModel->update($super_admin['id'], [
                    'otp_hash' => $hashed,
                    'otp_expires' => $expires,
                    'otp_failed_attempts' => 0,
                    'otp_locked_until' => null,
                    'admin_code' => $hashedAdminCode,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'SuperAdmin login: failed to generate/store OTP/admin_code: ' . $e->getMessage());
                $plainOtp = null;
                $newAdminCode = null;
            }

            // Send the one-time OTP to the superadmin's email. Use Brevo transactional API if available.
            if (!empty($super_admin['email']) && !empty($plainOtp)) {
                try {
                    require ROOTPATH . 'vendor/autoload.php';
                    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('BREVO_API_KEY'));
                    $api = new TransactionalEmailsApi(new GuzzleClient(), $config);
                    $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                    $html = '<p>Your one-time login code is: <b>' . esc($plainOtp) . '</b>. It expires in 10 minutes.</p>';
                    if (!empty($newAdminCode)) {
                        $html .= '<p>Your SuperAdmin code (rotated now) is: <b>' . esc($newAdminCode) . '</b>. This code will change on the next login.</p>';
                    }
                    $payload = new SendSmtpEmail([
                        'subject' => 'Your Super Admin Login OTP and Admin Code',
                        'sender' => ['name' => 'Super Admin Access', 'email' => $fromEmail],
                        'to' => [[ 'email' => $super_admin['email'] ]],
                        'htmlContent' => $html,
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

    // Show change-password page for superadmin
    public function changePassword()
    {
        $email = $this->request->getPost('email');
        return view('superadmin/change_password');
    }

    // Process set password for superadmin
    public function setPassword()
    {
        $admin = $model->where('email', $email)->first();

        if (! $admin) {
            // Do not reveal whether the account exists
            return $this->response->setJSON(['message' => 'If that email exists we have sent reset instructions.']);
        }

        // generate an OTP and email it
        $otp = $this->superAdminModel->generateLoginOtp();
        $adminId = $admin['id'];

        $update = [
            'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'otp_expires' => date('Y-m-d H:i:s', time() + 60 * 15),
            'otp_failed_attempts' => 0,
            'otp_locked_until' => null,
        ];

        $model->update($adminId, $update);

        // attempt to email via Brevo; if that fails we still return generic response
        try {
            $this->superAdminModel->emailOtpToAdmin($email, $otp);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send admin OTP: ' . $e->getMessage());
        }

        // Return a redirect when the account exists so the client can show the reset form
        $redirect = site_url('superadmin/reset-password') . '?email=' . urlencode($email);

        return $this->response->setJSON([
            'message' => 'If that email exists we have sent reset instructions.',
            'redirect' => $redirect,
        ]);
        // Store only the hashed admin code in DB
        $hashedCode = password_hash($newAdminCode, PASSWORD_DEFAULT);

        $data = [
            'admin_code' => $hashedCode,
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        $insertId = $superAdminModel->insert($data);

        // Send the plain admin code to the new superadmin's email (one-time)
        if (!empty($data['email'])) {
            try {
                require ROOTPATH . 'vendor/autoload.php';
                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('BREVO_API_KEY'));
                $api = new TransactionalEmailsApi(new GuzzleClient(), $config);
                $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                $payload = new SendSmtpEmail([
                    'subject' => 'Your SuperAdmin Code',
                    'sender' => ['name' => 'Super Admin Access', 'email' => $fromEmail],
                    'to' => [[ 'email' => $data['email'] ]],
                    'htmlContent' => '<p>Your admin code is: <b>' . esc($newAdminCode) . '</b>. This code is valid until the next successful login (it will rotate on each login).</p>'
                ]);
                $api->sendTransacEmail($payload);
            } catch (\Throwable $e) {
                log_message('error', 'Failed sending superadmin admin_code email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'SuperAdmin created successfully!');
    }

    /**
     * Handle AJAX/POST from forgot password form: generate OTP and email reset code.
     * Expects `email` POST param. Returns JSON.
     */
    public function forgot()
    {
        $email = $this->request->getPost('email');
        if (! $email) {
            return $this->response->setJSON(['error' => 'Email required']);
        }

        // Basic rate-limiting settings (tunable)
        $maxPerIp = 20;         // max requests per IP in window
        $ipWindow  = 3600;      // window for IP limit (seconds)
        $maxPerEmail = 5;       // max requests per email in window
        $emailWindow = 3600;    // window for email limit (seconds)
        $accountLockSeconds = 900; // lock account for 15 minutes when abused

        $ip = $this->request->getIPAddress();
        $cache = \Config\Services::cache();

        // Track per-IP rate
        $ipKey = 'superadmin_forgot_ip_' . $ip;
        $ipCount = (int) $cache->get($ipKey);
        if ($ipCount >= $maxPerIp) {
            return $this->response->setStatusCode(429)->setJSON(['error' => 'Too many requests. Try again later.']);
        }
        $cache->save($ipKey, $ipCount + 1, $ipWindow);

        $model = new SuperAdminModel();
        $row = $model->where('email', $email)->first();

        // If account exists, enforce per-account rate limiting and lockout
        $emailKey = 'superadmin_forgot_email_' . md5(strtolower(trim($email)));
        if ($row) {
            $lockedUntil = isset($row['otp_locked_until']) && $row['otp_locked_until'] ? strtotime($row['otp_locked_until']) : 0;
            if ($lockedUntil > time()) {
                return $this->response->setStatusCode(429)->setJSON(['error' => 'Too many attempts. Try again later.']);
            }

            $emailCount = (int) $cache->get($emailKey);
            if ($emailCount >= $maxPerEmail) {
                // Lock account in DB to prevent further OTP generation for a while
                try {
                    $model->update($row['id'], ['otp_locked_until' => date('Y-m-d H:i:s', time() + $accountLockSeconds)]);
                } catch (\Throwable $_) { }
                return $this->response->setStatusCode(429)->setJSON(['error' => 'Too many requests for this account. Try again later.']);
            }
            $cache->save($emailKey, $emailCount + 1, $emailWindow);
        }

        // If account not found, return generic response (no redirect)
        if (! $row) {
            return $this->response->setJSON(['success' => true, 'message' => 'If that email exists we will send reset instructions.']);
        }

        // Generate OTP and store hashed value
        $plain = $model->generateLoginOtp();
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        try {
            $model->update($row['id'], ['otp_hash' => $hash, 'otp_expires' => $expires, 'otp_failed_attempts' => 0, 'otp_locked_until' => null]);
        } catch (\Throwable $e) {
            log_message('error', 'SuperAdmin forgot: failed to store otp: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to process request']);
        }

        // Send via Brevo transactional email if configured
        if (!empty($row['email'])) {
            try {
                require ROOTPATH . 'vendor/autoload.php';
                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('BREVO_API_KEY'));
                $api = new TransactionalEmailsApi(new GuzzleClient(), $config);
                $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                $html = '<p>Your Super Admin password reset code is: <b>' . esc($plain) . '</b>. It expires in 15 minutes.</p>';
                $payload = new SendSmtpEmail([
                    'subject' => 'Super Admin Password Reset Code',
                    'sender' => ['name' => 'Super Admin', 'email' => $fromEmail],
                    'to' => [[ 'email' => $row['email'] ]],
                    'htmlContent' => $html,
                ]);
                $api->sendTransacEmail($payload);
            } catch (\Throwable $e) {
                log_message('error', 'SuperAdmin forgot: email send failed: ' . $e->getMessage());
                // don't treat as fatal — still return generic success
            }
        }

        // Reset per-email counter on successful handling to avoid accidental lock after legitimate use
        try { $cache->delete($emailKey); } catch (\Throwable $_) { }

        // For convenience: return a redirect instruction when the email exists
        $redirectUrl = base_url('superadmin/reset-password') . '?email=' . urlencode($row['email']);
        return $this->response->setJSON(['success' => true, 'message' => 'Reset instructions sent', 'redirect' => $redirectUrl]);
    }

    // Show reset form (enter email, code, new password)
    public function resetForm()
    {
        return view('superadmin/reset_password');
    }

    // Process password reset: expects email, code, password, confirm_password
    public function resetPassword()
    {
        $email = $this->request->getPost('email');
        $code = $this->request->getPost('code');
        $pw = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm_password');

        if (! $email || ! $code || ! $pw || $pw !== $confirm || strlen($pw) < 8) {
            return redirect()->back()->with('error', 'Invalid input or passwords do not match (min 8 chars)');
        }

        $model = new SuperAdminModel();
        $row = $model->where('email', $email)->first();
        if (! $row) {
            return redirect()->back()->with('error', 'Invalid code or email');
        }

        $otpHash = $row['otp_hash'] ?? null;
        $otpExpires = !empty($row['otp_expires']) ? strtotime($row['otp_expires']) : 0;
        if (! $otpHash || $otpExpires < time() || ! password_verify((string)$code, $otpHash)) {
            return redirect()->back()->with('error', 'Invalid or expired code');
        }

        // OK — update password and clear OTP fields
        try {
            $model->update($row['id'], ['password' => password_hash($pw, PASSWORD_DEFAULT), 'otp_hash' => null, 'otp_expires' => null]);
        } catch (\Throwable $e) {
            log_message('error', 'SuperAdmin resetPassword failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to update password');
        }

        return redirect()->to('/superadmin/login')->with('success', 'Password updated. Please login.');
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

                // If the account requires a password change, prefer DB flag `must_change_password`.
                if (!empty($row['must_change_password'])) {
                    session()->set('force_password_change', true);
                    return redirect()->to('/superadmin/change-password')->with('info', 'Please change your password.');
                }

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
        // Stored admin_code values are hashed; so verify against all rows.
        $candidates = $superAdminModel->findAll();
        $matched = null;
        foreach ($candidates as $cand) {
            if (isset($cand['admin_code']) && $cand['admin_code'] && password_verify((string)$code, $cand['admin_code'])) {
                $matched = $cand; break;
            }
        }
        if ($matched) {
            $sessData = [
                'superadmin_code_verified' => true,
                'superadmin_id'   => $matched['id'],
            ];
            if (isset($matched['first_name']))  $sessData['superadmin_first_name']  = $matched['first_name'];
            if (isset($matched['middle_name'])) $sessData['superadmin_middle_name'] = $matched['middle_name'];
            if (isset($matched['last_name']))   $sessData['superadmin_last_name']   = $matched['last_name'];
            session()->set($sessData);
            try { session()->regenerate(true); } catch (\Throwable $e) { }
            return redirect()->to('/superadmin/login');
        }

        return redirect()->back()->with('error', 'Invalid Admin Code');
    }
}
