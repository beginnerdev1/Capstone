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
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Find user
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $session->set([
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'logged_in' => true
                ]);
                return redirect()->to('/users'); // user dashboard
            } else {
                return redirect()->back()->with('error', 'Invalid password')->withInput();
            }
        } else {
            return redirect()->back()->with('error', 'User not found')->withInput();
        }
    }

    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
