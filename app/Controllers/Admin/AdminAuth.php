<?php
namespace App\Controllers\Admin; // FIX: This is the correct namespace for app/Controllers/Admin/AdminAuth.php

use App\Controllers\BaseController;
use App\Models\AdminModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminAuth extends BaseController{
    
    public function adminLogin()
    {
        // log_message('debug', 'AdminAuth controller loaded'); // Removing unnecessary log
        return view('admin/login'); // Changed from '/login' to 'admin/login' for consistency
        
    }

    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $adminModel = new \App\Models\AdminModel();
        $admin = $adminModel->where('email', $email)->first();

        if(!$admin){
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        //check if Email is in the database but not verified by the OTP
        if($admin['is_verified'] == 0){
            $otp = rand(100000, 999999);

            //save OTP + set expiry
            $adminModel->update($admin['id'], ['otp_code' => $otp, 'otp_expire' => date('y-m-d H:i:S', strtotime('+5 minutes'))]);

            //Store session for verification
            session()->set(['otp_admin_id' => $admin['id'], 'otp_email' => $email]);

            //Sending an Email using PHPMailer idk how it works
            require ROOTPATH . 'vendor/autoload.php'; // <- this thing is needed I think?
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                try{
                    $mail->isSMTP();
                    $mail->Host = getenv('SMTP_HOST');
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USER');
                    $mail->Password = getenv('SMTP_PASS');
                    $mail->SMTPSecure = 'tls'; // Corrected from 'tis'
                    $mail->Port = getenv('SMTP_PORT');

                    $mail->setFrom(getenv('SMTP_FROM'), 'Admin Verification'); 
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject ="Your Admin OTP Code";
                    $mail->Body = "<p>Your OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";
                    $mail->send();
                }catch (\PHPMailer\PHPMailer\Exception $e) {
                    log_message('error', "Mailer Error: {$mail->ErrorInfo}");
                }return redirect()->to('admin/verify-otp')->with('success', 'OTP sent to your email');

        }

        if ($admin['is_verified'] == 1) {
            if ($password && password_verify($password, $admin['password'])) {
                session()->set([
                    'admin_id' => $admin['id'],
                    'is_admin_logged_in' => true
                ]);

                // ✅ Check if they are still using the default password
                if (password_verify('123456', $admin['password'])) {
                    session()->setFlashdata('force_password_change', true);
                }

                return redirect()->to('admin/')->with('success', 'Login successful.');
            } else {
                return redirect()->back()->with('error', 'Invalid email or password.');
            }
        }
    }

    //Show OTP
    public function showOtpForm()
    {
        return view('admin/loginVerify');
    }

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
    //Sets password
   public function setPassword()
    {
        // 1. Get Session ID
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            // If session is lost, tell the user to re-login
            return $this->response->setJSON(['status' => 'error', 'message' => 'Session expired. Please login again.']);
        }
        
        // 2. Get Password Data
        $newPassword = $this->request->getPost('password'); 

        if (empty($newPassword)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password is required.']);
        }
        
        // 3. Validation
        if (strlen($newPassword) < 6) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        }
         if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $newPassword)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password must contain 6+ characters, one uppercase, one number, and one special character.'
            ]);
        }

        // 4. Load Model and Hash Password
        $adminModel = new \App\Models\AdminModel();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            // 5. Update the Password in the database
            $adminModel->update($adminId, [
                'password' => $hashedPassword
            ]);

            // 6. Clean up the flag that triggered the modal
            session()->setFlashdata('force_password_change', false); // Assuming the modal uses this key

            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Password updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            // Log the error for internal debugging
            log_message('error', 'Database update error in setPassword: ' . $e->getMessage());
            
            // Return a generic error to the frontend
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'A database error prevented the password from being saved.'
            ]);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
