<?php

namespace App\Controllers;

use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\PasswordResetModel;

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

    public function forgotPasswordForm()
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');

        //checking if email exists in users table
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email address not found.');
        }

        //generate token
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);

        //store token in password_resets table
        $resetModel = new \App\Models\PasswordResetModel();
        $resetModel->insert([
            'email' => $email,
            'token' => $hashedToken,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

         //reset link
        $resetLink = base_url("reset_password?token=$token&email=" . urlencode($email));

        //email the reset link
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SMTP_PORT');

            //Recipients
            $mail->setFrom(getenv('SMTP_FROM'), 'Password Reset');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "<h3>Password Reset Request</h3>
            <p>Click the link to reset your password: <a href=\"$resetLink\">$resetLink</a></p>
            <p>If you did not request a password reset, please ignore this email.</p>
            <h4>This link will expire in 1 hour.</h4>";

            $mail->send();
         
           /*  if ($mail->send()) {
                // ✅ Store a one-time success message
                return redirect()->back()->with('success', 'A password reset link has been sent to your email.');
            } else {
                // ✅ Store error if email fails
                return redirect()->back()->with('error', 'Failed to send reset email. Please try again.');
            }
 */
        } catch (Exception $e) {
            log_message('error', "Mailer Error: {$mail->ErrorInfo}");
        }
        
        return redirect()->to('/login')->with('message', 'Password reset link sent to your email.');
    }
}
