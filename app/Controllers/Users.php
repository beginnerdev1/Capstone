<?php   

namespace App\Controllers;

class Users extends BaseController
{
    public function index()
    {
        return view('users/index');
    }

    public function billing()
    {
        return view('users/billing');
    }

    public function payments()
    {
        return view('users/payments');
    }

    public function pressure()
    {
        return view('users/pressure');
    }

    public function report()
    {
        return view('users/report');
    }

    public function profile()
    {
        return view('users/profile');
    }

    public function changePassword()
    {
        // Logic to change user password
        return view('users/changepassword');
    }
    public function editprofile()
    {
        // Logic to update user settings
        return view('users/editprofile');
    }
}