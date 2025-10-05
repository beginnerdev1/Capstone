<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AdminModel;




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
        $adminModel = new \App\Models\AdminModel();

        $name     = trim($this->request->getPost('name') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $username = trim($this->request->getPost('username') ?? '');
        $position = trim($this->request->getPost('position') ?? '');

        // Check if all fields are filled
        if (!$name || !$email || !$username || !$position) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
        }

        // Check for duplicates one by one
        if ($adminModel->where('name', $name)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Name is already in use.'
            ]);
        }

        if ($adminModel->where('email', $email)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email is already in use.'
            ]);
        }

        if ($adminModel->where('username', $username)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Username is already in use.'
            ]);
        }

        if ($adminModel->where('position', $position)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Position is already in use.'
            ]);
        }

        // If no duplicates, insert new user
        $data = [
            'name'        => $name,
            'email'       => $email,
            'username'    => $username,
            'position'    => $position,
            'is_verified' => 0,
        ];

        if ($adminModel->insert($data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'User account created successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Something went wrong while creating the user.'
            ]);
        }
    }
    

    // Fetch users with pagination AJAX
    public function getUsers()
    {
        $model= new AdminModel();
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
