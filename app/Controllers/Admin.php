<?php

namespace App\Controllers;

use App\Models\BillingModel;

class Admin extends BaseController
{
    public function index()
    {
        if(!session()->get('is_admin_logged_in')){
            return redirect()->to('/admin/login');
        }
        return view('admin/index');
    }
    public function adminLogin()
    {
        return view('admin/login');
    }
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $adminModel = new \App\Models\AdminModel();
        $admin = $adminModel->where('email', $email)->first();

        if($admin && password_verify($password, $admin['password'])){
            session->set([
                'admin_id' => $admin['id'],
                'admin_email' => $admin['email'],
                'is_admin_logged_in' => true
            ]);
            return redirect()->to('/index');
        }
        return redirect()->back()->with('error', 'Invalid email or password');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
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
    {   //load user model (panag pag tawag ng object sa java)
        $userModel = new \App\Models\UserModel();
        //para sa pag kuha ng  registered users
        $data['users'] = $userModel->getRegisteredUsers();

        return view('admin/registeredUsers', $data);
    }
    public function billings()
    {  //para sa unpaid bills
         $billingModel = new BillingModel();
        $data['billings'] = $billingModel->getUnpaidBills();
        return view('admin/billings', $data);
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
