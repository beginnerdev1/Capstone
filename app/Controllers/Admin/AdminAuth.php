<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminAuth extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    // Show login view
    public function adminLogin()
    {
        return view('admin/login');
    }

    // Handles login + OTP flow
    public function login()
    {
        $email = $this->p('email');
        $password = $this->p('password');

        $admin = $this->adminModel->where('email', $email)->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        // Not verified → send OTP
        if ($admin['is_verified'] == 0) {
            $otp = rand(100000, 999999);
            $this->adminModel->update($admin['id'], [
                'otp_code'   => $otp,
                'otp_expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);

            session()->set([
                'otp_admin_id' => $admin['id'],
                'otp_email'    => $email
            ]);

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
                $mail->setFrom(getenv('SMTP_FROM'), 'Admin Verification');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Your Admin OTP Code";
                $mail->Body    = "<p>Your OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";
                $mail->send();
            } catch (Exception $e) {
                log_message('error', "Mailer Error: {$mail->ErrorInfo}");
            }

            return redirect()->to('admin/verify-otp')->with('success', 'OTP sent to your email.');
        }

        // Verified admin → check password
        if (!password_verify($password, $admin['password'])) {
            return redirect()->back()->with('error', 'Invalid Email or Password.');
        }

        $defaultPassword = '123456';
        $forceChange = password_verify($defaultPassword, $admin['password']);

        session()->set([
            'admin_id'              => $admin['id'],
            'is_admin_logged_in'    => true,
            'force_password_change' => $forceChange
        ]);

        if ($forceChange) {
            return redirect()->to('admin/')->with('warning', 'Please change your default password.');
        }

        return redirect()->to('admin/')->with('success', 'Login successful.');
    }

    public function showOtpForm()
    {
        return view('admin/loginVerify');
    }

    public function verifyOtp()
    {
        $otp = $this->p('otp');
        $adminId = session()->get('otp_admin_id');

        if (!$adminId) {
            return redirect()->to('admin/login')->with('error', 'Session expired. Please try again.');
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return redirect()->to('admin/login')->with('error', 'Account not found. Please try again.');
        }

        if ($admin['otp_code'] == $otp && strtotime($admin['otp_expire']) > time()) {
            $this->adminModel->update($adminId, [
                'is_verified' => 1,
                'otp_code'    => null,
                'otp_expire'  => null
            ]);

            session()->remove(['otp_admin_id', 'otp_email']);
            session()->set([
                'admin_id' => $adminId,
                'is_admin_logged_in' => true
            ]);

            $updatedAdmin = $this->adminModel->find($adminId);
            if (empty($updatedAdmin['password'])) {
                session()->setFlashdata('force_password_change', true);
            }

            return redirect()->to('admin/')
                ->with('success', 'Email verified! Welcome to the dashboard.');
        }

        return redirect()->to('admin/verify-otp')->with('error', 'Invalid or expired OTP.');
    }

    public function resendOtp()
    {
        $adminId = session()->get('otp_admin_id');
        $email   = session()->get('otp_email');

        if (!$adminId || !$email) {
            return redirect()->to('admin/login')->with('error', "Session expired. Please login again.");
        }

        $otp = rand(100000, 999999);
        $this->adminModel->update($adminId, [
            'otp_code'   => $otp,
            'otp_expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
        ]);

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
            $mail->setFrom(getenv('SMTP_FROM'), 'Admin Verification');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Your Admin OTP Code";
            $mail->Body    = "<p>Your OTP is <b>$otp</b>. It will expire in 5 minutes.</p>";
            $mail->send();
        } catch (Exception $e) {
            log_message('error', "Mailer Error: {$mail->ErrorInfo}");
        }

        return redirect()->to('admin/verify-otp')->with('success', 'OTP successfully resent to your email.');
    }

    public function changePassword()
    {
        if (!session()->get('is_admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        return view('admin/changePassword');
    }

    public function setPassword()
    {
        $adminId = session()->get('admin_id');
        $newPassword = $this->p('password');

        if (!$adminId || !$newPassword) {
            return redirect()->back()->with('error', 'Invalid request.');
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->adminModel->update($adminId, ['password' => $hashed]);

        $session = session();
        $session->set('force_password_change', false);
        $session->setFlashdata('success', 'Password changed successfully.');
        $session->close();

        return redirect()->to('admin/');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
