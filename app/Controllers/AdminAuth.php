<?php
namespace App\Controllers;
use App\Models\AdminModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminAuth extends BaseController{

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
        // FIX: Retrieve data using getPost() because the frontend uses URLSearchParams (form data).
        $newPassword = $this->request->getPost('password'); 

        if (empty($newPassword)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password is required.']);
        }

        if (strlen($newPassword) < 6) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        }

        // 3. Load Model and Hash Password
        $adminModel = new \App\Models\AdminModel();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            // 4. Update the Password in the database
            $adminModel->update($adminId, [
                'password' => $hashedPassword
                // You may also want to unset 'is_verified' if needed, or add a 'password_set' flag.
            ]);

            // 5. Clean up the flag that triggered the modal
            session()->setFlashdata('show_password_modal', false);

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

    
    public function adminLogin()
    {
        return view('admin/login');
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
                    $mail->SMTPSecure = 'tis';
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

        if($admin['is_verified'] == 1){
            if($password && password_verify($password, $admin['password'])){
                session()->set(['admin_id' => $admin['id'], 'is_admin_logged_in' => true]);

                return redirect()->to('admin/')->with('success', 'Login successful.');
            }
            else{return redirect()->back()->with('error', 'Invalid email or password');}
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
            return redirect()->back()->with('error', 'Account not found.');
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
            $showModal = false;

            // ✅ Use fresh data from DB to ensure password check is up-to-date
            $updatedAdmin = $adminModel->find($adminId);

            if (empty($updatedAdmin['password']) || is_null($updatedAdmin['password'])) {
                $showModal = true;
            }

            return redirect()->to('admin/')
                ->with('success', 'Email verified! Welcome to the dashboard.');
        }

        // 9. Fallback for invalid or expired OTP
        return redirect()->to('admin/login')->with('error', 'Invalid or expired OTP.');
    }

    public function resendOtp()
    {
        $adminId = session()->get('otp_admin_id');
        $email   = session()->get('otp_email');

        if(!$admin || !$email){
            return redirect()->to('admin/login')->with('error', "Session expired. Please login again");
        }

         $otp = rand(100000, 999999);
         $adminModel = new AdminModel();
         $adminModel->update($admin['id'], ['otp_code' => $otp, 'otp_expire' => date('y-m-d H:i:S', strtotime('+5 minutes'))]);

         $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                try{
                    $mail->isSMTP();
                    $mail->Host = getenv('SMTP_HOST');
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USER');
                    $mail->Password = getenv('SMTP_PASS');
                    $mail->SMTPSecure = 'tis';
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
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}





?>