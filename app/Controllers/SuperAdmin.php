<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SuperAdmin extends Controller
{
    public function index()
    {
        return view('superadmin/index'); // dashboard home
    }
    
    public function loginForm()
    {
        return view('superadmin/login');
    }

    public function checkCodeForm()
    {
        return view('superadmin/check_code');
    }


    public function dashboard()
    {
        return view('superadmin/dashboard');
    }

    public function users()
    {
        return view('superadmin/users');
    }

    public function settings()
    {
        return view('superadmin/settings');
    }
}
