<?php
namespace App\Controllers;
use App\Models\AdminModel;

class AdminAuth extends BaseController{

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

        if ($admin && password_verify($password, $admin['password'])) {
            // ✅ Generate OTP (6-digit)
            $otp = rand(100000, 999999);

            // Save OTP + expiry (5 min) in session or DB
            session()->set([
                'otp_admin_id' => $admin['id'],
                'otp_code' => $otp,
                'otp_expires' => time() + 300 // 5 mins
            ]);

            // Send OTP email (use PHPMailer or CI4 Email)
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = getenv('SMTP_HOST');
                $mail->SMTPAuth   = true;
                $mail->Username   = getenv('SMTP_USER');
                $mail->Password   = getenv('SMTP_PASS');
                $mail->SMTPSecure = 'tls';
                $mail->Port       = getenv('SMTP_PORT');

                $mail->setFrom(getenv('SMTP_FROM'), 'Admin OTP');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your Admin OTP Code';
                $mail->Body    = "<p>Your OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";

                $mail->send();
            } catch (\Exception $e) {
                log_message('error', "Mailer Error: {$mail->ErrorInfo}");
            }

            // Redirect to OTP verification form
            return redirect()->to('/admin/loginVerify')->with('success', 'OTP sent to your email.');
        }

        return redirect()->back()->with('error', 'Invalid email or password');
    }
    public function loginVerify()
    {
       $otp = $this->request->getPost('otp');
       if(session()->get('otp_code')&&session()->get('otp_expires')>time()&&$otp==session()->get('otp_code')){
        // if OTP is valid
        session()->set([
            'admin_id' => session()->get('otp_admin_id'),
            'is_admin_logged_in' => true
        ]);
        // Clear OTP session data
        session()->remove(['otp_code','otp_expires','otp_admin_id']);
        return redirect()->to('/admin')->with('success','Login successful');
       }

         return redirect()->back()->with('error','Invalid or expired OTP');
    }
    public function showOtpForm()
    {
        return view('admin/loginVerify');
    }
    public function resendOtp()
    {
        $adminId = session()->get('otp_admin_id');
        $email   = session()->get('admin_email');

        if (!$adminId || !$email) {
            return redirect()->to('/admin/login')->with('error', 'Session expired. Please login again.');
        }

        // Generate a new OTP
        $otp = rand(100000, 999999);

        // Update OTP in session
        session()->set([
            'otp_code'   => $otp,
            'otp_expires'=> time() + 300 // valid 5 mins
        ]);

        // Send OTP email
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SMTP_PORT');

            $mail->setFrom(getenv('SMTP_FROM'), 'Admin OTP');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Admin OTP Code (Resent)';
            $mail->Body    = "<p>Your new OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";

            $mail->send();
        } catch (\Exception $e) {
            log_message('error', "Mailer Error (Resend): {$mail->ErrorInfo}");
            return redirect()->back()->with('error', 'Failed to resend OTP.');
        }

        return redirect()->back()->with('success', 'A new OTP has been sent to your email.');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}





?>