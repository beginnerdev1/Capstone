<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SuperAdminModel;
use App\Models\AdminModel;

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

    public function loginForm(){
        if(!session()->get('superadmin_code_verified')){
            return redirect()->to('/superadmin/check-code')->with('error', 'Please verify your admin code first.');
        }
        return view('superadmin/login');
    }

    public function login()
    {   
        $this->checkCode();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $code = session()->get('superadmin_code'); // get code from session

        $SuperAdminModel = new SuperAdminModel();
        $super_admin = $SuperAdminModel->where('email', $email)->first();
        if ($super_admin && password_verify($password, $super_admin['password']) && $code === $super_admin['admin_code']) {
            // set session
            session()->set([
                'superadmin_id' => $super_admin['id'],
                'superadmin_email' => $super_admin['email'],
                'is_superadmin_logged_in' => true,
            ]);

            session()->remove('superadmin_code_verified'); 
            return redirect()->to('/superadmin'); 
        }
        return redirect()->back()->with('error', 'Invalid email or password'); // login page
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

    public function createAccount($role = 'admin')
    {
        $adminModel = new \App\Models\AdminModel();
        $superAdminModel = new SuperAdminModel();

        // Generate code prefix depending on role
        $prefix = ($role === 'super_admin') ? 'SUP-A' : 'ADM-A';
        $newAdminCode = $adminModel->generateAdminCode($prefix);

        $data = [
            'admin_code' => $newAdminCode,
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => $role,
        ];
        if($role !== 'super_admin'){
            $adminModel->insert($data);
        }else{
           $superAdminModel->insert($data);
        }
        

        return redirect()->back()->with('success', ucfirst($role) . ' created successfully!');
    }

    public function checkCodeForm()
    {
        return view('superadmin/check_code');
    }

    public function checkCode()
    {
        $code = $this->request->getPost('admin_code');
        $superAdminModel = new SuperAdminModel();
        $super_admin = $superAdminModel->where('admin_code', $code)->where('role', 'super_admin')->first();

        if($super_admin){
            session()->set([
                'superadmin_code_verified' => true,
                'superadmin_id' => $super_admin['id'],
                'superadmin_code' => $super_admin['admin_code']
            ]);
            return redirect()->to('/superadmin/login');
        }

        return redirect()->back()->with('error', 'Invalid Admin Code');
    }
}
