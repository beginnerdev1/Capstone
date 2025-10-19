<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;

class Admin extends BaseController
{
    protected $usersModel;
    protected $userInfoModel;
    protected $billingModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->userInfoModel = new UserInformationModel();
        $this->billingModel = new BillingModel();
    }

    public function index()
    {
        $billingModel = new \App\Models\BillingModel();

        // Get monthly totals
        $query = $billingModel->select("
                DATE_FORMAT(updated_at, '%b') AS month,
                SUM(amount_due) AS total
            ")
            ->where('status', 'Paid')
            ->groupBy('month')
            ->orderBy('updated_at', 'ASC')
            ->get();

        $months = [];
        $incomeData = [];

        foreach ($query->getResultArray() as $row) {
            $months[] = $row['month'];
            $incomeData[] = (float) $row['total'];
        }

        $data = [
            'title' => 'Dashboard',
            'months' => $months,
            'incomeData' => $incomeData,
        ];

        // ✅ Render the page (which extends the layout)
        return view('admin/index', $data);
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
        $usersModel = new \App\Models\UsersModel();
        $userInfoModel = new \App\Models\UserInformationModel();

        $puroks = ['1', '2', '3', '4', '5'];
        $selectedPurok = $this->request->getGet('purok');

        $builder = $usersModel
            ->select('users.id, user_information.first_name, user_information.last_name, user_information.email, user_information.barangay, user_information.municipality, user_information.purok')
            ->join('user_information', 'user_information.user_id = users.id', 'left');

        if ($selectedPurok) {
            $builder->where('user_information.purok', $selectedPurok);
        }

        $data = [
            'title' => 'Registered Users',
            'users' => $builder->findAll(),
            'puroks' => $puroks,
            'selectedPurok' => $selectedPurok
        ];

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

     public function manageAccounts()
    {
        $usersModel = new \App\Models\UsersModel();
        $userInfoModel = new \App\Models\UserInformationModel();

        $builder = $usersModel
        ->select('users.id, users.is_verified, user_information.first_name, user_information.last_name, user_information.email, user_information.phone, user_information.barangay, user_information.purok')
        ->join('user_information', 'user_information.user_id = users.id', 'left')
        ->orderBy('user_information.last_name', 'ASC');


        $data = [
            'title' => 'Manage Accounts',
            'users' => $builder->findAll(),
        ];

        return view('admin/ManageAccounts', $data);
    }
 
   public function announcements()
    {
        $data['title'] = 'Announcements';
        return view('admin/announcements', $data);
    }
  
    public function reports()
    {
        $data = [
            'title' => 'Reports',
        ];

        return view('admin/layouts/main', [
            'title' => 'Reports',
            'content' => view('admin/Reports', $data)
        ]);
    }
    public function changePasswordView()
    {
        return view('admin/change_password');
    }
}
