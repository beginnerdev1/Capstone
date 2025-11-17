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

        $defaultPassword = '123456';
        $forceChange = password_verify($defaultPassword, $admin['password']);

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
            $logId = $logModel->insert([
                'actor_type' => 'admin',
                'actor_id'   => $admin['id'],
                'action'     => 'login',
                'route'      => '/admin/login',
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
            if (empty($updatedAdmin['password'])) {
                session()->setFlashdata('force_password_change', true);
            }

            // Log login after OTP verification
            try {
                $logModel = new AdminActivityLogModel();
                $logId = $logModel->insert([
                    'actor_type' => 'admin',
                    'actor_id'   => $adminId,
                    'action'     => 'login',
                    'route'      => '/admin/verify-otp',
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
        $this->adminModel->update($adminId, ['password' => $hashed]);

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
            if ($logId) {
                $logModel = new AdminActivityLogModel();
                $logModel->update($logId, ['logged_out_at' => date('Y-m-d H:i:s')]);
                session()->remove('admin_activity_log_id');
            }
        } catch (\Throwable $e) { }
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
