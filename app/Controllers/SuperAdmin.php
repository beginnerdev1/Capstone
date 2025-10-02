<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SuperAdmin extends Controller
{
    public function index()
    {
        return view('superadmin/index'); // dashboard
    }
    public function dashboard()
    {
        return view('superadmin/dashboard'); // dashboard
    }

    public function users()
    {
        return view('superadmin/users'); // manage users
    }

    public function settings()
    {
        return view('superadmin/settings'); // settings
    }

    public function login()
    {
        return view('superadmin/login'); // login page
    }

    public function logout()
    {
        // destroy session then redirect to login
        session()->destroy();
        return redirect()->to('/superadmin/login');
    }

    public function forgotPassword()
    {
        return view('superadmin/forgot_password'); // forgot password page
    }
}
