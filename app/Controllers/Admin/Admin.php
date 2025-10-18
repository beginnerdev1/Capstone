<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;

class Admin extends BaseController
{
    public function index()
    {
        if(!session()->get('is_admin_logged_in')){
            return redirect()->to('admin/login');
        }
        return view('admin/index');
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
    public function page500()
    {
        return view('admin/500');
    }
    public function tables()
    {
        return view('admin/tables');
    }
   public function registeredUsers()
    {
        $userModel = new \App\Models\UserModel();

        // Example purok list
        $puroks = ['1', '2', '3', '4', '5'];
        $selectedPurok = $this->request->getGet('purok');

        if ($selectedPurok) {
            $data['users'] = $userModel->where('Purok', $selectedPurok)->findAll();
        } else {
            $data['users'] = $userModel->findAll();
        }

        $data['puroks'] = $puroks;
        $data['selectedPurok'] = $selectedPurok;

        return view('admin/registeredUsers', $data);
    }
     public function getUserInfo()
    {
        $userId = $this->request->getGet('user_id'); 
        $username = $this->request->getGet('username');

        $userModel = new \App\Models\UserModel();

        // You can search by either ID or username
        $user = null;
        if (!empty($userId)) {
            $user = $userModel->where('id', $userId)->first();
        } elseif (!empty($username)) {
            $user = $userModel->where('username', $username)->first();
        }

        if ($user) {
            // Join with user_information if needed
            $userInfoModel = new \App\Models\UserInformationModel();
            $info = $userInfoModel->where('user_id', $user['id'])->first();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'email' => $user['email'] ?? ($info['email'] ?? ''),
                    'address' => $info['address'] ?? '',
                    'username' => $user['username'],
                    'id' => $user['id']
                ]
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
    }
 
   
  
   
    public function reports()
    {
        $db = \Config\Database::connect();

        // Build query with JOIN to users table
        $builder = $db->table('service_reports sr')
            ->select('sr.id, sr.issue_type, sr.description, sr.status, sr.address, sr.latitude, sr.longitude, sr.created_at, u.username as user')
            ->join('users u', 'u.id = sr.user_id', 'left') // left join in case user is missing
            ->orderBy('sr.created_at', 'DESC');

        $data['reports'] = $builder->get()->getResultArray();

        return view('admin/Reports', $data);
    }
    public function changePasswordView()
    {
        return view('admin/change_password');
    }
}
