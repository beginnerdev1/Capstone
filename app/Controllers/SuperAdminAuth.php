<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SuperAdminModel;
use App\Models\AdminModel;

class SuperAdminAuth extends Controller
{
    public function loginForm()
    {
        if (!session()->get('superadmin_code_verified')) {
            return redirect()->to('/superadmin/check-code')
                             ->with('error', 'Please verify your admin code first.');
        }
        return view('superadmin/login');
    }

    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $code     = session()->get('superadmin_code');

        $SuperAdminModel = new SuperAdminModel();
        $super_admin = $SuperAdminModel->where('email', $email)->first();

        if ($super_admin && password_verify($password, $super_admin['password']) && $code === $super_admin['admin_code']) {
            session()->set([
                'superadmin_id'          => $super_admin['id'],
                'superadmin_email'       => $super_admin['email'],
                'is_superadmin_logged_in'=> true,
            ]);

            session()->remove('superadmin_code_verified');
            return redirect()->to('/superadmin');
        }

        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/superadmin/login');
    }

    public function forgotPassword()
    {
        return view('superadmin/forgot_password');
    }

    public function createAccount()
    {
        $superAdminModel = new SuperAdminModel();
        $newAdminCode    = $superAdminModel->generateAdminCode();

        $data = [
            'admin_code' => $newAdminCode,
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        $superAdminModel->insert($data);

        return redirect()->back()->with('success', 'SuperAdmin created successfully!');
    }

    public function checkCodeForm()
    {
        return view('superadmin/check_code');
    }

    public function checkCode()
    {
        $code = $this->request->getPost('admin_code');

        $superAdminModel = new SuperAdminModel();
        $super_admin = $superAdminModel->where('admin_code', $code)->first();

        if ($super_admin) {
            session()->set([
                'superadmin_code_verified' => true,
                'superadmin_id'   => $super_admin['id'],
                'superadmin_code' => $super_admin['admin_code'],
            ]);
            return redirect()->to('/superadmin/login');
        }

        return redirect()->back()->with('error', 'Invalid Admin Code');
    }
}
