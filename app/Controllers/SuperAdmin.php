<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AdminModel;
use App\Models\SuperadminListAdminUserModel;



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

    // Handle user creation AJAX
    public function createUser()
    {
        helper('form');
        $adminModel = new AdminModel();

        $data = [
            'name'        => $this->request->getPost('name'),
            'email'       => $this->request->getPost('email'),
            'username'    => $this->request->getPost('username'),
            'position'    => $this->request->getPost('position'),
            'is_verified' => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($adminModel->insert($data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'User account created successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to create user account.'
            ]);
        }
    }

    // Fetch users with pagination AJAX
    public function getUsers()
    {
        $model = new SuperadminListAdminUserModel();
        $users = $model->orderBy('created_at', 'DESC')->findAll();

        return $this->response->setJSON($users);
    }

    public function settings()
    {
        return view('superadmin/settings');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/superadmin/login');
    }

}
