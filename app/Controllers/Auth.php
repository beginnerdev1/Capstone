<?php

namespace App\Controllers;

use App\Models\UsersModel;
use PHPMailer\PHPMailer\PHPMailer;
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
            $this->userModel->protect(false)->update($userId, [
                'otp_code'    => null,
                'otp_expires' => null,
            ]);

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

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Update OTP and expiration time (5 minutes)
        $this->userModel->update($userId, [
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        ]);

        // Send the OTP via Brevo
        $this->sendOtpEmail($user['email'], $otp);

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
}
