<?php

namespace App\Controllers;

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
        return view('admin/registeredUsers');
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
   public function getBillings()
    {
        $billingModel = new BillingModel();

        $billings = $billingModel->select('billings.id, users.username, billings.amount, billings.due_date')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->orderBy('billings.created_at', 'DESC')
            ->findAll();

        // Sample test data
        $sample = [
            ['id' => 1, 'username' => 'Test', 'amount' => 1500, 'due_date' => '2025-11-10']
        ];

        // Return actual or sample data
        return $this->response->setJSON(['data' => $billings ?: $sample]);
    }
   public function createBilling()
    {
        $billingModel = new BillingModel();

        $data = [
            'user_id'    => $this->request->getPost('user_id'),
            'amount'     => $this->request->getPost('amount'),
            'description'=> $this->request->getPost('description') ?? 'N/A',
            'created_at' => date('Y-m-d H:i:s'),
            'due_date'   => date('Y-m-d H:i:s', strtotime('+1 month')),
        ];

        if ($billingModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Bill created successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to create bill.']);
        }
    }

    public function billings()
    {  //para sa ma-ipakita yung unpaid bills
       
        return view('admin/billings');
    }
    public function paidBills()
    {   //para sa paid bills
        $billingModel = new BillingModel();
        $data['billings'] = $billingModel->getPaidBills();
        return view('admin/paidBills', $data);
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
    
}
