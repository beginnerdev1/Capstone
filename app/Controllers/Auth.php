<?php

namespace App\Controllers;

use App\Models\UserModel;
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
    $userModel = new UserModel();
    $email     = $this->request->getPost('email');
    $password  = $this->request->getPost('password');

    // 🔹 Validate inputs: Check if email or password is empty
    if (empty($email) || empty($password)) {
        return redirect()->back()->with('error', 'Email and password are required.')->withInput();
    }

    // 🔹 Check if email exists
    $user = $userModel->where('email', $email)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
    }

    // 🔹 Check if password is correct
    if (!password_verify($password, $user['password'])) {
        return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
    }

    // 🔹 If both are correct, log in
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
        $userModel = new \App\Models\UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // 🔹 Validate password requirements (at least 8 characters, 1 uppercase, 1 lowercase, 1 number)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            return redirect()->back()->with('error', 'Password does not meet the requirements.')->withInput();
        }

        // 🔹 Check if email already exists
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Email is already registered.')->withInput();
        }

        // 🔹 Check if passwords match
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

        // Removed: $userInfoModel->insert([...]) since only email and password are required

        session()->set('pending_user', $userId);
        $this->sendOtpEmail($email, $otp);

        return redirect()->to(base_url('verify'))
            ->with('message', 'OTP sent to your email');
    }


    // 🔹 Verify OTP
    public function verify()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }

        return view('users/verify');
    }
    

    public function verifyOtp()
{
    // Create an instance of the UserModel to interact with the users table
    $userModel = new UserModel();

    // Get the pending user's ID from the session
    $userId = session()->get('pending_user');

    // Retrieve the user record from the database using the ID
    $user = $userModel->find($userId);

    // If the user doesn't exist (e.g., session expired), redirect to register with an error message
    if (!$user) {
        return redirect()->to(base_url('register'))
            ->with('error', 'Session expired. Please register again.');
    }

    // Get the OTP input submitted by the user from the POST request
    $inputOtp = $this->request->getPost('otp');

    // Check if the OTP matches the one in the database and hasn't expired
    if ($user['otp_code'] == $inputOtp && strtotime($user['otp_expires']) > time()) {

        // Update the user: mark as verified and clear the OTP fields
        $userModel->update($userId, [
            'is_verified' => 1,   // Mark user as verified
            'otp_code'    => null, // Clear the OTP code
            'otp_expires' => null, // Clear OTP expiry
        ]);

        // Set session data to log the user in
        session()->set([
            'isLoggedIn' => true,     // User is now logged in
            'user_id'    => $user['id'], // Store user ID in session
            'email'      => $user['email'], // Store email in session
        ]);

        // Remove the pending_user session variable as it's no longer needed
        session()->remove('pending_user');

        // Redirect to user dashboard with a success message
        return redirect()->to(base_url('users'))
            ->with('message', 'Account verified and logged in successfully!');
    } else {
        // If OTP is invalid or expired, clear the OTP fields
        // Use protect(false) to allow updating fields not in $allowedFields
        $userModel->protect(false)->update($userId, [
            'otp_code'    => null, // Clear OTP code
            'otp_expires' => null, // Clear OTP expiry
        ]);

        // Redirect back to verification page with an error message
        return redirect()->back()->with('error', 'Invalid or expired OTP. Please request a new one.');
    }
}


    // 🔹 Resend OTP
    public function resendOtp()
    {
        if (!session()->has('pending_user')) {
            return redirect()->to('/register');
        }

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('pending_user'));

        if (! $user) {
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
