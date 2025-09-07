<?php

namespace App\Controllers;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Auth extends BaseController
{

    // Handle Login
    public function login()
    {
        return view('users/login'); // must exist: app/Views/users/login.php
    }

    public function attemptLogin()
    {
        $userModel = new UserModel();
        $email     = $this->request->getPost('email');
        $password  = $this->request->getPost('password');

        // Find user by email
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            session()->set([
                'isLoggedIn' => true,
                'user_id'    => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
            ]);

            return redirect()->to(base_url('users'))->with('message', 'Logged in successfully!');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }



    public function registerForm()
    {
        return view('users/register'); // must exist: app/Views/user/register.php
    }


    // Handle Registration

    public function register()
    {
        $userModel = new UserModel();

        $otp = rand(100000, 999999);

        $data = [
            'username'    => $this->request->getPost('username'),
            'email'       => $this->request->getPost('email'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_verified' => 0,
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', time() + 300) // 5 mins
        ];

        if (! $userModel->insert($data)) {
            // show validation/db errors if insert fails
            dd($userModel->errors());
        }

        $userId = $userModel->getInsertID();

        if ($userId) {
            session()->set('pending_user', $userId);

            // send OTP to email
            $this->sendOtpEmail($data['email'], $otp);

            return redirect()->to(base_url('verify'))
                 ->with('message', 'OTP sent to your email');
        }

        return redirect()->back()->with('error', 'Registration failed, try again.');
    }



    public function verify()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }
        return view('users/verify'); // must exist: app/Views/user/verify.php
    }

    //Vrify OTP
    public function verifyOtp()
    {
        $userModel = new UserModel();
        $userId    = session()->get('pending_user');

        $user = $userModel->find($userId);

        if (! $user) {
                    return redirect()->to(base_url('register'))
                         ->with('error', 'Session expired. Please register again.');
        }

        $inputOtp = $this->request->getPost('otp');

        // ✅ Check if OTP is correct and not expired
        if (
            $user['otp_code'] == $inputOtp &&
            strtotime($user['otp_expires']) > time()
        ) {
            // Success → verify account & clear OTP
            $userModel->update($userId, [
                'is_verified' => 1,
                'otp_code'    => null,
                'otp_expires' => null
            ]);

            // Auto login
            session()->set([
                'isLoggedIn' => true,
                'user_id'    => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email']
            ]);

            session()->remove('pending_user');

           return redirect()->to(base_url('users'))->with('message', 'Account verified and logged in successfully!');
        } else {
            // ❌ Expired or wrong OTP → clear old OTP
            $userModel->update($userId, [
                'otp_code'    => null,
                'otp_expires' => null
            ]);

            return redirect()->back()->with('error', 'Invalid or expired OTP. Please request a new one.');
        }
    }


    // Resend OTP

    public function resendOtp()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }

        $userModel = new UserModel();
        $user      = $userModel->find(session()->get('pending_user'));

        if (! $user) {
            return redirect()->to('/register')->with('error', 'User not found.');
        }

        $otp = rand(100000, 999999);

        // ✅ Save OTP in database with 5 min expiry
        $userModel->update($user['id'], [
            'otp_code'    => $otp,
            'otp_expires' => date('Y-m-d H:i:s', time() + 300) // 5 minutes
        ]);

        $this->sendOtpEmail($user['email'], $otp);

        return redirect()->to('/verify')->with('message', 'A new OTP has been sent.');
    }

    private function sendOtpEmail($toEmail, $otp)
    {
        require ROOTPATH . 'vendor/autoload.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

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
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            log_message('error', "Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

}
