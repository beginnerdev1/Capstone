<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index() {
        return view('admin/index');  // Load the admin dashboard view
    }

    public function login() {
        return view('admin/login');  // Load the admin login view
    }

    public function charts() {
        return view('admin/charts');  // Load the charts page for admin
    }

    // Add methods for other pages like 404, 401, etc.
    public function page404() {
        return view('admin/404');  // Load 404 page
    }

    public function page401() {
        return view('admin/401');  // Load 401 page
    }
}
