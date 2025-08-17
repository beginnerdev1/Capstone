<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        return view('admin/index');
    }

    public function login()
    {
        return view('admin/login');
    }


    public function layoutStatic()
    {
        return view('admin/layout-static');
    }

    public function charts()
    {
        return view('admin/charts');
    }

    public function page404()
    {
        return view('admin/404');
    }

    public function page401()
    {
        return view('admin/401');
    }
}
