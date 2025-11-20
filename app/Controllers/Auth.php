<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\PasswordResetModel;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;  

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
    }

    // 🔹 Show login form
    public function login()
    {
        return view('users/login');
    }

    // 🔹 Attempt login
    public function attemptLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate inputs
        if (empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Email and password are required.')->withInput();
        }

        // Check if email exists
        $user = $this->userModel->where('email', $email)->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
        }

        // Login
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'email'      => $user['email'],
        ]);

        return redirect()->to(base_url('users'))->with('message', 'Logged in successfully!');
    }

    // 🔹 Show register form
    public function registerForm()
    {
        return view('users/register');
    }

    // 🔹 Handle registration
    public function register()
    {
        $email           = $this->request->getPost('email');
        $password        = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validate password
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            return redirect()->back()->with('error', 'Password does not meet the requirements.')->withInput();
        }

        // Check email
        if ($this->userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Email is already registered.')->withInput();
        }

        // Validate email format and domain deliverability
        helper('email');
        if (!is_real_email((string)$email)) {
            return redirect()->back()->with('error', 'Please provide a valid email address.')->withInput();
        }

        // Passwords match
        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Confirm password does not match.')->withInput();
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        $userData = [
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'is_verified' => 0,
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', time() + 300),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if (!$this->userModel->insert($userData)) {
            dd($this->userModel->errors());
        }

        $userId = $this->userModel->getInsertID();
        session()->set('pending_user', $userId);
        $this->sendOtpEmail($email, $otp);

        return redirect()->to(base_url('verify'))->with('message', 'OTP sent to your email');
    }

    // 🔹 Show OTP verification form
    public function verify()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }

        return view('users/verify');
    }

    // 🔹 Verify OTP
    public function verifyOtp()
    {
        $userId = session()->get('pending_user');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('register'))->with('error', 'Session expired. Please register again.');
        }

        $inputOtp = $this->request->getPost('otp');

        // Lockout protection
        $lockedUntil = (int) (session()->get('user_otp_locked_until') ?? 0);
        if ($lockedUntil > time()) {
            $wait = $lockedUntil - time();
            return redirect()->to('/verify')->with('error', 'Too many attempts. Try again in ' . $wait . ' seconds.');
        }

        if ($user['otp_code'] == $inputOtp && strtotime($user['otp_expires']) > time()) {
            $this->userModel->update($userId, [
                'is_verified' => 1,
                'otp_code'    => null,
                'otp_expires' => null,
            ]);

            session()->set([
                'isLoggedIn' => true,
                'user_id'    => $user['id'],
                'email'      => $user['email'],
            ]);

            session()->remove('pending_user');

            return redirect()->to(base_url('users'))->with('message', 'Account verified and logged in successfully!');
        } else {
            // Track failures and set a short lockout if needed
            $fails = (int) (session()->get('user_otp_fail_count') ?? 0);
            $fails++;
            session()->set('user_otp_fail_count', $fails);

            // Invalidate OTP to avoid brute force on same code
            $this->userModel->protect(false)->update($userId, [
                'otp_code'    => null,
                'otp_expires' => null,
            ]);

            if ($fails >= 5) {
                session()->set('user_otp_locked_until', time() + 300);
            }

            return redirect()->back()->with('error', 'Invalid or expired OTP. Please request a new one.');
        }
    }

    // 🔹 Resend OTP email Using Brevo
    public function resendOtp()
    {
        // Check if there's a pending user in session
        if (!session()->has('pending_user')) {
            return redirect()->to('/register')->with('error', 'Session expired. Please register again.');
        }

        // Fetch user details
        $userId = session()->get('pending_user');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/register')->with('error', 'User not found.');
        }

        // Enforce simple lockout/cooldown
        $now = time();
        $lockedUntil = (int) (session()->get('user_otp_locked_until') ?? 0);
        if ($lockedUntil > $now) {
            $wait = $lockedUntil - $now;
            return redirect()->to('/verify')->with('error', 'Too many attempts. Try again in ' . $wait . ' seconds.');
        }
        $lastReq = (int) (session()->get('user_otp_last') ?? 0);
        if ($now - $lastReq < 60) {
            $wait = 60 - ($now - $lastReq);
            return redirect()->to('/verify')->with('error', 'Please wait ' . $wait . ' seconds before requesting a new OTP.');
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Update OTP and expiration time (5 minutes)
        $this->userModel->update($userId, [
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        ]);

        // Send the OTP via Brevo
        $this->sendOtpEmail($user['email'], $otp);

        session()->set('user_otp_last', $now);
        session()->remove('user_otp_fail_count');

        // Redirect with success message
        return redirect()->to('/verify')->with('message', 'A new OTP has been sent to your email.');
    }


// 🔹 Send OTP email using Brevo
    private function sendOtpEmail($toEmail, $otp)
    {
        require ROOTPATH . 'vendor/autoload.php';

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', getenv('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );

        $email = new SendSmtpEmail([
            'subject' => 'Account Verification - One Time Password (OTP)',
            'sender' => ['name' => 'Water Billing System', 'email' => getenv('SMTP_FROM')],
            'to' => [['email' => $toEmail]],
            'htmlContent' => "
                <h3>Account Verification</h3>
                <p>Dear user,</p>
                <p>To complete your account verification process, please use the following One Time Password (OTP):</p>
                <h2><b>{$otp}</b></h2>
                <p>This code will expire in 5 minutes. Please do not share this code with anyone.</p>
                <br>
                <p>Thank you for using our Water Billing System.</p>
                <small>This is an automated message. Please do not reply.</small>
            ",
        ]);

        try {
            $apiInstance->sendTransacEmail($email);
        } catch (Exception $e) {
            log_message('error', 'Email could not be sent. Error: ' . $e->getMessage());
        }
    }


    // 🔹 Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    //---------------Forgot Password-------------------------

    // Show forgot-password form
    public function forgotPasswordForm()
    {
        $actor = $this->request->getGet('actor') ?? 'user';
        return view('users/forgot_password', ['actor' => $actor]);
    }

    // Show reset form (GET /reset?email=...&token=...)
    public function resetForm()
    {
        $email = $this->request->getGet('email');
        $token = $this->request->getGet('token');
        $actor = $this->request->getGet('actor') ?? 'user';

        if (empty($email) || empty($token)) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid reset link.');
        }

        $prModel = new PasswordResetModel();
        $tokenHash = hash('sha256', $token);

        $row = $prModel->where('email', $email)
                       ->where('token', $tokenHash)
                       ->where('actor', $actor)
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->first();

        if (!$row) {
            return redirect()->to('/forgot-password')->with('error', 'Reset link is invalid or has expired.');
        }

        return view('users/reset', ['email' => $email, 'token' => $token, 'actor' => $actor]);
    }

    // Handle forgot-password form submit: create token + send email
    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        $actor = $this->request->getPost('actor') ?? 'user';
        if (empty($email)) {
            return redirect()->back()->with('error', 'Please provide an email address.');
        }

        // Verify existence depending on actor
        if ($actor === 'admin') {
            $usersModel = new \App\Models\AdminModel();
            $user = $usersModel->where('email', $email)->first();
        } else {
            $usersModel = new UsersModel();
            $user = $usersModel->where('email', $email)->first();
        }

        // Always return generic message to avoid email enumeration
        if (!$user) {
            return redirect()->back()->with('message', 'If that email exists we sent a reset link.');
        }

        // Basic rate-limiting (cache-based) to avoid abuse
        try {
            $cache = \Config\Services::cache();
            $key = 'password_reset_req_' . md5($email . $this->request->getIPAddress());
            if ($cache->get($key)) {
                return redirect()->back()->with('message', 'Please wait a minute before requesting another reset link.');
            }
            $cache->save($key, 1, 60);
        } catch (\Throwable $e) {
            log_message('error', 'Password reset rate-limit check failed: ' . $e->getMessage());
        }

        $prModel = new PasswordResetModel();

        try {
            try {
                $prModel->where('email', $email)->where('actor', $actor)->delete();
            } catch (\Throwable $e) {
                log_message('error', 'PasswordReset cleanup failed: ' . $e->getMessage());
            }
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
            'actor' => $actor,
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
            $resetLink = base_url('/reset') . '?email=' . urlencode($email) . '&token=' . urlencode($token) . '&actor=' . urlencode($actor);

            // Log link in non-production to aid development (do not enable in production)
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                log_message('debug', 'Password reset link for ' . $email . ': ' . $resetLink);
            }

            $payload = new \Brevo\Client\Model\SendSmtpEmail([
                'subject' => 'Reset your password',
                'sender'  => ['name' => 'Water Billing System', 'email' => $fromEmail],
                'to'      => [[ 'email' => $email ]],
                'htmlContent' => "<p>You requested a password reset. Click the link below to set a new password (link expires in 1 hour):</p>
                                  <p><a href=\"{$resetLink}\">Reset password</a></p>
                                  <p>If you didn't request this, ignore this email.</p>"
            ]);

            $api->sendTransacEmail($payload);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send reset email: ' . $e->getMessage());
        }

        return redirect()->back()->with('message', 'If that email exists we sent a reset link.');
    }

    // Handle actual password update (POST /reset-password)
    public function processResetPassword()
    {
        $email = $this->request->getPost('email');
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm_password') ?? '';

        $actor = $this->request->getPost('actor') ?? 'user';

        if (empty($email) || empty($token) || empty($password) || empty($confirm)) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        if ($password !== $confirm) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        // Password policy: at least 8 chars, upper/lower, number (adjust if needed)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            return redirect()->back()->with('error', 'Password must be at least 8 characters, contain upper/lower and a number.');
        }

        $prModel = new PasswordResetModel();
        $tokenHash = hash('sha256', $token);

        $row = $prModel->where('email', $email)
                       ->where('token', $tokenHash)
                       ->where('actor', $actor)
                       ->where('expires_at >', date('Y-m-d H:i:s'))
                       ->first();

        if (!$row) {
            return redirect()->to('/forgot-password')->with('error', 'Reset token invalid or expired.');
        }

        // Update the correct actor's account
        if ($actor === 'admin') {
            $adminModel = new \App\Models\AdminModel();
            $record = $adminModel->where('email', $email)->first();
            if (!$record) {
                try { $prModel->where('email', $email)->where('actor', 'admin')->delete(); } catch (\Throwable $e) { log_message('error', 'PasswordReset cleanup failed: ' . $e->getMessage()); }
                return redirect()->to('/forgot-password')->with('error', 'Admin account not found.');
            }

            try {
                $adminModel->update($record['id'], [
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ]);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Failed to update password.');
            }

            try {
                $prModel->where('email', $email)->where('actor', 'admin')->delete();
            } catch (\Throwable $e) { log_message('error', 'PasswordReset cleanup failed: ' . $e->getMessage()); }

            return redirect()->to('/admin/login')->with('message', 'Your password has been reset. You may now log in.');
        } else {
            $usersModel = new UsersModel();
            $user = $usersModel->where('email', $email)->first();
            if (!$user) {
                try { $prModel->where('email', $email)->where('actor', 'user')->delete(); } catch (\Throwable $e) { log_message('error', 'PasswordReset cleanup failed: ' . $e->getMessage()); }
                return redirect()->to('/forgot-password')->with('error', 'User account not found.');
            }

            try {
                $usersModel->update($user['id'], [
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ]);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Failed to update password.');
            }

            try {
                $prModel->where('email', $email)->where('actor', 'user')->delete();
            } catch (\Throwable $e) { log_message('error', 'PasswordReset cleanup failed: ' . $e->getMessage()); }

            return redirect()->to('/login')->with('message', 'Your password has been reset. You may now log in.');
        }
    }
}
