<?php
namespace App\Controllers\Admin; // FIX: This is the correct namespace for app/Controllers/Admin/AdminAuth.php

use App\Controllers\BaseController;
use App\Models\AdminModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminAuth extends BaseController{
    //shows login view
    public function adminLogin()
    {
        // log_message('debug', 'AdminAuth controller loaded'); // Removing unnecessary log
        return view('admin/login'); // Changed from '/login' to 'admin/login' for consistency
        
    }

    //handles login, including OTP verification, default password check, and session management
   public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $adminModel = new \App\Models\AdminModel();
        $admin = $adminModel->where('email', $email)->first();

        // 1️⃣ If email not found
        if (!$admin) {
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        // 2️⃣ If not verified, just send OTP — no password needed yet
        if ($admin['is_verified'] == 0) {
            $otp = rand(100000, 999999);
            $adminModel->update($admin['id'], [
                'otp_code'   => $otp,
                'otp_expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);

            session()->set([
                'otp_admin_id' => $admin['id'],
                'otp_email'    => $email
            ]);

            // Send OTP email
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
                $mail->setFrom(getenv('SMTP_FROM'), 'Admin Verification');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Your Admin OTP Code";
                $mail->Body    = "<p>Your OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";
                $mail->send();
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                log_message('error', "Mailer Error: {$mail->ErrorInfo}");
            }

            return redirect()->to('admin/verify-otp')->with('success', 'OTP sent to your email.');
        }

        // 3️⃣ If verified, then require password
        if (!password_verify($password, $admin['password'])) {
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        // 4️⃣ Check if still using the default password
        $defaultPassword = '123456';
        $forceChange = password_verify($defaultPassword, $admin['password']);

        // 5️⃣ Set session values once
        session()->set([
            'admin_id'              => $admin['id'],
            'is_admin_logged_in'    => true,
            'force_password_change' => $forceChange
        ]);

        log_message('debug', '✅ Login successful — session: ' . json_encode(session()->get()));

        // 6️⃣ Redirect based on password status
        if ($forceChange) {
            return redirect()->to('admin/')->with('warning', 'Please change your default password.');
        }

        return redirect()->to('admin/')->with('success', 'Login successful.');
    }

    //Show OTP
    public function showOtpForm()
    {
        return view('admin/loginVerify');
    }

    //Verify OTP
    public function verifyOtp()
    {
        $otp = $this->request->getPost('otp');
        $adminId = session()->get('otp_admin_id');

        // 1. Check session first
        if (!$adminId) {
            return redirect()->to('admin/login')->with('error', 'Session expired. Please try again.');
        }

        // 2. Load the model and find the admin
        $adminModel = new AdminModel();
        $admin = $adminModel->find($adminId);

        if (!$admin) {
            return redirect()->to('admin/login')->with('error', 'Account not found. Please try again.');
        }

        // 3. Validate OTP and expiry
        if ($admin['otp_code'] == $otp && strtotime($admin['otp_expire']) > time()) {
            // 4. Mark verified, clear OTP
            $adminModel->update($adminId, [
                'is_verified' => 1,
                'otp_code' => null,
                'otp_expire' => null
            ]);

            // 5. Clear temporary OTP-related session data
            session()->remove(['otp_admin_id', 'otp_email']);

            // 6. Log in automatically
            session()->set([
                'admin_id' => $adminId,
                'is_admin_logged_in' => true
            ]);

            // 7. Determine if the password needs to be set
            $updatedAdmin = $adminModel->find($adminId);

            if (empty($updatedAdmin['password'])) {
                session()->setFlashdata('force_password_change', true);
            }

            return redirect()->to('admin/')
                ->with('success', 'Email verified! Welcome to the dashboard.');
        }

        // 9. Fallback for invalid or expired OTP
        return redirect()->to('admin/verify-otp')->with('error', 'Invalid or expired OTP.');
    }

    //Resend OTP
    public function resendOtp()
    {
        $adminId = session()->get('otp_admin_id');
        $email   = session()->get('otp_email');

        if(!$adminId || !$email){
            return redirect()->to('admin/login')->with('error', "Session expired. Please login again.");
        }

         $otp = rand(100000, 999999);
         $adminModel = new AdminModel();
         // Fetch admin record to get the ID for update
         $admin = $adminModel->find($adminId); 

         if (!$admin) {
             return redirect()->to('admin/login')->with('error', 'Account not found. Please login again.');
         }

         $adminModel->update($adminId, ['otp_code' => $otp, 'otp_expire' => date('y-m-d H:i:S', strtotime('+5 minutes'))]);

         require ROOTPATH . 'vendor/autoload.php';
         $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                try{
                    $mail->isSMTP();
                    $mail->Host = getenv('SMTP_HOST');
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USER');
                    $mail->Password = getenv('SMTP_PASS');
                    $mail->SMTPSecure = 'tls'; // Corrected
                    $mail->Port = getenv('SMTP_PORT');

                    $mail->setFrom(getenv('SMTP_FROM'), 'Admin Verification'); 
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject ="Your Admin OTP Code";
                    $mail->Body = "<p>Your OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";
                    $mail->send();
                }catch (\PHPMailer\PHPMailer\Exception $e) {
                    log_message('error', "Mailer Error: {$mail->ErrorInfo}");
                }
        return redirect()->to('admin/verify-otp')->with('success', 'OTP successfully resent to your email.');
        
    }

    //Opens change password view
    public function changePassword()
    {
        // optional: prevent access if not logged in
        if (!session()->get('is_admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        return view('admin/changePassword');
    }

    //Sets password
    public function setPassword()
    {
        $adminModel = new \App\Models\AdminModel();
        $adminId = session()->get('admin_id');
        $newPassword = $this->request->getPost('password');

        if (!$adminId || !$newPassword) {
            return redirect()->back()->with('error', 'Invalid request.');
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $adminModel->update($adminId, ['password' => $hashed]);

        $session = session();
        $session->set('force_password_change', false);
        $session->setFlashdata('success', 'Password changed successfully.');
        
        // Force session write before redirect
        $session->close();

        return redirect()->to('admin/');
    }

    //Logs out admin
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
