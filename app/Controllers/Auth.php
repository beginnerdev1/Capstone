<?php

namespace App\Controllers;

class Auth extends BaseController
{
    // Show login form
    public function login()
    {
        return view('users/login'); // loads app/Views/login.php
    }
}
