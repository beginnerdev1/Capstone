<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index() {
        return view('admin/index');  // This will load the admin dashboard (index.php)
    }

    public function login() {
        return view('admin/login');  // This will load the login page
    }

    public function charts() {
        return view('admin/charts');  // This will load the charts page
    }

    public function page404() {
        return view('admin/404');  // This will load the 404 page
    }

    public function page401() {
        return view('admin/401');  // This will load the 401 page
    }
}
