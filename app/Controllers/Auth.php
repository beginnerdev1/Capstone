<?php

namespace App\Controllers;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Auth extends BaseController
{
    public function login()
    {
        return view('users/login'); // must exist: app/Views/user/login.php
    }

    public function attemptLogin()
    {
        $userModel = new UserModel();
        $username  = $this->request->getPost('username');
        $password  = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password']) && $user['is_verified']) {
            session()->set([
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'logged_in' => true,
            ]);
            return redirect()->to('/users'); // change if your dashboard is different
        }

        return redirect()->back()->with('error', 'Invalid login or account not verified.');
    }

    public function registerForm()
    {
        return view('users/register'); // must exist: app/Views/user/register.php
    }

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

            return redirect()->to('/verify')->with('message', 'OTP sent to your email.');
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

    public function verifyOtp()
    {
        $otpInput   = $this->request->getPost('otp');
        $otpSession = session()->get('otp_code');
        $expires    = session()->get('otp_expires');
        $userId     = session()->get('pending_user');

        if ($otpInput == $otpSession && time() < $expires) {
            $userModel = new UserModel();
            $userModel->update($userId, ['is_verified' => 1]);

            session()->remove(['otp_code', 'otp_expires', 'pending_user']);
            return redirect()->to('/login')->with('message', 'Account verified! You can now log in.');
        }

        return redirect()->back()->with('error', 'Invalid or expired OTP.');
    }

    public function resendOtp()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }

        $otp = rand(100000, 999999);
        session()->set([
            'otp_code'    => $otp,
            'otp_expires' => time() + 60
        ]);

        $userModel = new UserModel();
        $user      = $userModel->find(session()->get('pending_user'));
        $this->sendOtpEmail($user['email'], $otp);

        return redirect()->to('/verify')->with('message', 'A new OTP has been sent.');
    }

    private function sendOtpEmail($toEmail, $otp)
    {
        // ✅ Fix: correct path for autoload
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
            $mail->Body    = "<h3>Your OTP code is: <b>{$otp}</b></h3><p>This code will expire in 1 minute.</p>";

            $mail->send();
        } catch (Exception $e) {
            log_message('error', "Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
