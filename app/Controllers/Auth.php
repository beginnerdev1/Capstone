<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    // Show login form
    public function login()
    {
        return view('users/login');
    }

    // Handle form submit
    public function attemptLogin()
    {
        helper(['form', 'url']);
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Find user by username
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Start session only if login is successful
            $session = session(); // CodeIgniter handles session instance
            $session->set([
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'logged_in' => true,
            ]);

            return redirect()->to('/users'); // Redirect to dashboard
        }

        // ❌ Wrong username or password
        return redirect()->back()
                         ->with('error', 'Invalid username or password')
                         ->withInput();
    }

    // Logout
    public function logout()
    {
        $session = session();
        if ($session->has('logged_in')) {
            $session->destroy(); // ✅ Clear session completely
        }
        return redirect()->to('/login');
    }
}
