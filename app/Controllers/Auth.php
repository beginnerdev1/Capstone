<?php

namespace App\Controllers;

use App\Models\UsersModel; // ✅ Correct model
use PHPMailer\PHPMailer\PHPMailer;

class Auth extends BaseController
{
    // 🔹 Show login form
    public function login()
    {
        return view('users/login');
    }

    // 🔹 Attempt login
    public function attemptLogin()
    {
        $userModel = new UsersModel();
        $email     = $this->request->getPost('email');
        $password  = $this->request->getPost('password');

        // 🔹 Validate inputs
        if (empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Email and password are required.')->withInput();
        }

        // 🔹 Check if email exists
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
        }

        // 🔹 Check password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
        }

        // 🔹 Login
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
        $userModel = new UsersModel(); // ✅ Correct

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // 🔹 Validate password
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            return redirect()->back()->with('error', 'Password does not meet the requirements.')->withInput();
        }

        // 🔹 Check email
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Email is already registered.')->withInput();
        }

        // 🔹 Passwords match
        if ($password !== $confirmPassword) {
            return redirect()->back()->with('error', 'Confirm password does not match.')->withInput();
        }

        $otp = rand(100000, 999999);

        $userData = [
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'is_verified' => 0,
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', time() + 300),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if (!$userModel->insert($userData)) {
            dd($userModel->errors());
        }

        $userId = $userModel->getInsertID();

        session()->set('pending_user', $userId);
        $this->sendOtpEmail($email, $otp);

        return redirect()->to(base_url('verify'))
            ->with('message', 'OTP sent to your email');
    }

    // 🔹 Verify OTP view
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
        $userModel = new UsersModel(); // ✅ Correct
        $userId = session()->get('pending_user');
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('register'))
                ->with('error', 'Session expired. Please register again.');
        }

        $inputOtp = $this->request->getPost('otp');

        if ($user['otp_code'] == $inputOtp && strtotime($user['otp_expires']) > time()) {

            $userModel->update($userId, [
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

            return redirect()->to(base_url('users'))
                ->with('message', 'Account verified and logged in successfully!');
        } else {
            $userModel->protect(false)->update($userId, [
                'otp_code'    => null,
                'otp_expires' => null,
            ]);

            return redirect()->back()->with('error', 'Invalid or expired OTP. Please request a new one.');
        }
    }

    // 🔹 Resend OTP
    public function resendOtp()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }

        $userModel = new UsersModel(); // ✅ Correct
        $user = $userModel->find(session()->get('pending_user'));

        if (!$user) {
            return redirect()->to('/register')->with('error', 'User not found.');
        }

        $otp = rand(100000, 999999);

        $userModel->update($user['id'], [
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', time() + 300),
        ]);

        $this->sendOtpEmail($user['email'], $otp);

        return redirect()->to('/verify')->with('message', 'A new OTP has been sent.');
    }

    // 🔹 Send OTP email
    private function sendOtpEmail($toEmail, $otp)
    {
        require ROOTPATH . 'vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SMTP_PORT');

            $mail->setFrom(getenv('SMTP_FROM'), 'Account Verification');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "<h3>Your OTP code is: <b>{$otp}</b></h3><p>This code will expire in 5 minutes.</p>";

            $mail->send();
        } catch (\Exception $e) {
            log_message('error', "Mailer Error: {$mail->ErrorInfo}");
        }
    }

    // 🔹 Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
