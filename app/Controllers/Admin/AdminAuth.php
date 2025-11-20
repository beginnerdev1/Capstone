<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\AdminActivityLogModel;

class AdminAuth extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    // Show login view
    public function adminLogin()
    {
        // If already logged in as admin, send them to dashboard
        if (session()->get('is_admin_logged_in')) {
            return redirect()->to('admin/')->with('info', 'You are already logged in.');
        }

        return view('admin/login');
    }

    // Handles login + OTP flow
    public function login()
    {
        $email = $this->p('email');
        $password = $this->p('password');

        $admin = $this->adminModel->where('email', $email)->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        // Not verified → send OTP
        if ($admin['is_verified'] == 0) {
            // Cooldown: allow OTP send once every 60s
            $now = time();
            $lastReq = (int) (session()->get('admin_login_otp_last') ?? 0);
            if ($now - $lastReq < 60) {
                $wait = 60 - ($now - $lastReq);
                return redirect()->back()->with('error', 'Please wait ' . $wait . ' seconds before requesting OTP again.');
            }

            $otp = rand(100000, 999999);
            $this->adminModel->update($admin['id'], [
                'otp_code'   => $otp,
                'otp_expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);

            session()->set([
                'otp_admin_id' => $admin['id'],
                'otp_email'    => $email
            ]);

            // Send via Brevo Transactional Email API
            require ROOTPATH . 'vendor/autoload.php';
            try {
                $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('BREVO_API_KEY'));
                $api = new TransactionalEmailsApi(new GuzzleClient(), $config);
                $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                $payload = new SendSmtpEmail([
                    'subject' => 'Your Admin OTP Code',
                    'sender' => ['name' => 'Admin Verification', 'email' => $fromEmail],
                    'to' => [[ 'email' => $email ]],
                    'htmlContent' => '<p>Your OTP is <b>' . $otp . '</b>. It will expire in 5 minutes.</p>'
                ]);
                $api->sendTransacEmail($payload);
            } catch (\Throwable $e) {
                log_message('error', 'Brevo AdminAuth OTP send failed: ' . $e->getMessage());
            }

            session()->set('admin_login_otp_last', $now);
            session()->remove('admin_login_otp_fail_count');
            session()->remove('admin_login_otp_locked_until');

            return redirect()->to('admin/verify-otp')->with('success', 'OTP sent to your email.');
        }

        // Verified admin → check password
        if (!password_verify($password, $admin['password'])) {
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        // Default password for newly-created admin accounts is configurable via env
        $defaultPassword = getenv('DEFAULT_PASSWORD') ?: '123456';
        // Prefer explicit DB flag for forced password change when available
        $forceChange = !empty($admin['must_change_password']) ? true : password_verify($defaultPassword, $admin['password']);

        session()->set([
            'admin_id'              => $admin['id'],
            'is_admin_logged_in'    => true,
            'force_password_change' => $forceChange
        ]);

        if ($forceChange) {
            return redirect()->to('admin/')->with('warning', 'Please change your default password.');
        }

        // Log login
        try {
            $logModel = new AdminActivityLogModel();
            $resource = $this->request->getVar('file') ?? $this->request->getVar('path') ?? basename($this->request->getUri()->getPath());
            $logId = $logModel->insert([
                'actor_type' => 'admin',
                'actor_id'   => $admin['id'],
                'action'     => 'login',
                'route'      => '/admin/login',
                'resource'   => $resource,
                'method'     => 'POST',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => substr((string)($this->request->getUserAgent() ?? ''), 0, 255),
            ], true);
            session()->set('admin_activity_log_id', $logId);
        } catch (\Throwable $e) { }

        return redirect()->to('admin/')->with('success', 'Login successful.');
    }

    public function showOtpForm()
    {
        return view('admin/loginVerify');
    }

    // Show admin forgot-password form (reuses users view but forces actor=admin)
    public function forgotPasswordForm()
    {
        return view('users/forgot_password', ['actor' => 'admin']);
    }

    // Handle admin forgot-password POST (creates admin-scoped reset token)
    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        if (empty($email)) {
            return redirect()->back()->with('error', 'Please provide an email address.');
        }

        // Verify admin exists
        $admin = $this->adminModel->where('email', $email)->first();
        if (!$admin) {
            return redirect()->back()->with('message', 'If that email exists we sent a reset link.');
        }

        // Rate limiting (reuse same cache key logic)
        try {
            $cache = \Config\Services::cache();
            $key = 'password_reset_req_admin_' . md5($email . $this->request->getIPAddress());
            if ($cache->get($key)) {
                return redirect()->back()->with('message', 'Please wait a minute before requesting another reset link.');
            }
            $cache->save($key, 1, 60);
        } catch (\Throwable $e) {
            log_message('error', 'Admin password reset rate-limit check failed: ' . $e->getMessage());
        }

        $prModel = new \App\Models\PasswordResetModel();

        try {
            $prModel->where('email', $email)->where('actor', 'admin')->delete();
        } catch (\Throwable $e) {
            log_message('error', 'PasswordReset cleanup failed: ' . $e->getMessage());
        }

        try {
            $token = bin2hex(random_bytes(32));
        } catch (\Throwable $e) {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }

        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $prModel->insert([
            'email' => $email,
            'token' => $tokenHash,
            'actor' => 'admin',
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Send reset email (Brevo)
        try {
            require ROOTPATH . 'vendor/autoload.php';
            $config = \Brevo\Client\Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', getenv('BREVO_API_KEY'));
            $api = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

            $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
            $resetLink = base_url('/reset') . '?email=' . urlencode($email) . '&token=' . urlencode($token) . '&actor=admin';

            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                log_message('debug', 'Admin password reset link for ' . $email . ': ' . $resetLink);
            }

            $payload = new \Brevo\Client\Model\SendSmtpEmail([
                'subject' => 'Reset your admin password',
                'sender'  => ['name' => 'Water Billing System', 'email' => $fromEmail],
                'to'      => [[ 'email' => $email ]],
                'htmlContent' => "<p>You requested an admin password reset. Click the link below to set a new password (link expires in 1 hour):</p>
                                  <p><a href=\"{$resetLink}\">Reset password</a></p>
                                  <p>If you didn't request this, ignore this email.</p>"
            ]);

            $api->sendTransacEmail($payload);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send admin reset email: ' . $e->getMessage());
        }

        return redirect()->back()->with('message', 'If that email exists we sent a reset link.');
    }

    public function verifyOtp()
    {
        $otp = $this->p('otp');
        $adminId = session()->get('otp_admin_id');

        if (!$adminId) {
            return redirect()->to('admin/login')->with('error', 'Session expired. Please try again.');
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return redirect()->to('admin/login')->with('error', 'Account not found. Please try again.');
        }

        $lockedUntil = (int) (session()->get('admin_login_otp_locked_until') ?? 0);
        if ($lockedUntil > time()) {
            $wait = $lockedUntil - time();
            return redirect()->to('admin/verify-otp')->with('error', 'Too many attempts. Try again in ' . $wait . ' seconds.');
        }

        if ($admin['otp_code'] == $otp && strtotime($admin['otp_expire']) > time()) {
            $this->adminModel->update($adminId, [
                'is_verified' => 1,
                'otp_code'    => null,
                'otp_expire'  => null
            ]);

            session()->remove(['otp_admin_id', 'otp_email']);
            session()->set([
                'admin_id' => $adminId,
                'is_admin_logged_in' => true
            ]);

            $updatedAdmin = $this->adminModel->find($adminId);
            if (!empty($updatedAdmin['must_change_password'])) {
                session()->setFlashdata('force_password_change', true);
                session()->set('force_password_change', true);
            }

            // Log login after OTP verification
            try {
                $logModel = new AdminActivityLogModel();
                $resource = $this->request->getVar('file') ?? $this->request->getVar('path') ?? basename($this->request->getUri()->getPath());
                $logId = $logModel->insert([
                    'actor_type' => 'admin',
                    'actor_id'   => $adminId,
                    'action'     => 'login',
                    'route'      => '/admin/verify-otp',
                    'resource'   => $resource,
                    'method'     => 'POST',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => substr((string)($this->request->getUserAgent() ?? ''), 0, 255),
                ], true);
                session()->set('admin_activity_log_id', $logId);
            } catch (\Throwable $e) { }

            return redirect()->to('admin/')
                ->with('success', 'Email verified! Welcome to the dashboard.');
        }

        // Count failures and lock after 5 attempts
        $fails = (int) (session()->get('admin_login_otp_fail_count') ?? 0);
        $fails++;
        session()->set('admin_login_otp_fail_count', $fails);
        if ($fails >= 5) {
            session()->set('admin_login_otp_locked_until', time() + 300);
        }
        return redirect()->to('admin/verify-otp')->with('error', 'Invalid or expired OTP.');
    }

    public function resendOtp()
    {
        $adminId = session()->get('otp_admin_id');
        $email   = session()->get('otp_email');

        if (!$adminId || !$email) {
            return redirect()->to('admin/login')->with('error', "Session expired. Please login again.");
        }

        // Enforce cooldown and lockout
        $now = time();
        $lockedUntil = (int) (session()->get('admin_login_otp_locked_until') ?? 0);
        if ($lockedUntil > $now) {
            $wait = $lockedUntil - $now;
            return redirect()->to('admin/verify-otp')->with('error', 'Too many attempts. Try again in ' . $wait . ' seconds.');
        }
        $lastReq = (int) (session()->get('admin_login_otp_last') ?? 0);
        if ($now - $lastReq < 60) {
            $wait = 60 - ($now - $lastReq);
            return redirect()->to('admin/verify-otp')->with('error', 'Please wait ' . $wait . ' seconds before requesting OTP again.');
        }

        $otp = rand(100000, 999999);
        $this->adminModel->update($adminId, [
            'otp_code'   => $otp,
            'otp_expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
        ]);

        require ROOTPATH . 'vendor/autoload.php';
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('BREVO_API_KEY'));
            $api = new TransactionalEmailsApi(new GuzzleClient(), $config);
            $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
            $payload = new SendSmtpEmail([
                'subject' => 'Your Admin OTP Code',
                'sender' => ['name' => 'Admin Verification', 'email' => $fromEmail],
                'to' => [[ 'email' => $email ]],
                'htmlContent' => '<p>Your OTP is <b>' . $otp . '</b>. It will expire in 5 minutes.</p>'
            ]);
            $api->sendTransacEmail($payload);
        } catch (\Throwable $e) {
            log_message('error', 'Brevo AdminAuth resend OTP failed: ' . $e->getMessage());
        }

        session()->set('admin_login_otp_last', $now);
        session()->remove('admin_login_otp_fail_count');

        return redirect()->to('admin/verify-otp')->with('success', 'OTP successfully resent to your email.');
    }

    public function changePassword()
    {
        if (!session()->get('is_admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        return view('admin/changePassword');
    }

    public function setPassword()
    {
        $adminId = session()->get('admin_id');
        $newPassword = $this->p('password');

        if (!$adminId || !$newPassword) {
            return redirect()->back()->with('error', 'Invalid request.');
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->adminModel->update($adminId, ['password' => $hashed, 'must_change_password' => 0]);

        $session = session();
        $session->set('force_password_change', false);
        $session->setFlashdata('success', 'Password changed successfully.');
        $session->close();

        return redirect()->to('admin/');
    }

    public function logout()
    {
        try {
            $logId = session()->get('admin_activity_log_id');
            $adminId = session()->get('admin_id');
            $logModel = new AdminActivityLogModel();
            $now = date('Y-m-d H:i:s');

            if ($logId) {
                $row = $logModel->find($logId);
                $details = [];
                if (!empty($row['details'])) {
                    try { $details = json_decode($row['details'], true) ?: []; } catch (\Throwable $_) { $details = []; }
                }
                $details[] = [
                    'action' => 'logout',
                    'time'   => $now
                ];
                session()->set('skip_activity_logger', true);
                $logModel->update($logId, ['details' => json_encode($details), 'logged_out_at' => $now]);
                session()->remove('admin_activity_log_id');
            } else {
                // No active session log — create a short logout record for auditing
                session()->set('skip_activity_logger', true);
                $resource = $this->request->getVar('file') ?? $this->request->getVar('path') ?? basename($this->request->getUri()->getPath());
                $logModel->insert([
                    'actor_type' => 'admin',
                    'actor_id'   => $adminId ?? null,
                    'action'     => 'logout',
                    'route'      => '/admin/logout',
                    'resource'   => $resource,
                    'method'     => $this->request->getMethod(),
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => substr((string)($this->request->getUserAgent() ?? ''), 0, 255),
                    'details'    => json_encode([['action' => 'logout', 'time' => $now]]),
                    'logged_out_at' => $now
                ], true);
            }
        } catch (\Throwable $e) { }
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
