<?php

namespace App\Controllers;

use App\Models\BillingModel;

class Admin extends BaseController
{
    public function index()
    {
        return view('admin/index');
    }public function login()
    {
        return view('admin/login');
    }public function layoutStatic()
    {
        return view('admin/layout-static');
    } public function charts()
    {
        return view('admin/charts');
    }public function page404()
    {
        return view('admin/404');
    }public function page401()
    {
        return view('admin/401');
    }public function page500(){
        return view('admin/500');
    } public function tables(){
        return view('admin/tables');
    } public function registeredUsers()
    {
        //inayos ko yung pag kuha ng query
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $query = $builder->select('id, username, email, phone_number, created_at, updated_at')->get();

        $data = [
            'users' => $query->getResultArray()
        ];
        return view('admin/registeredUsers', $data);
    } public function billings()
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
        $data = [
            'reports' => [
                [
                    'user' => 'John Doe',
                    'problem' => 'No water supply',
                    'date' => '2025-08-10',
                    'location' => 'Main St, Cityville',
                    'latitude' => 37.7749,
                    'longitude' => -122.4194
                ],
                [
                    'user' => 'Jane Smith',
                    'problem' => 'Low pressure',
                    'date' => '2025-08-15',
                    'location' => '2nd Ave, Cityville',
                    'latitude' => 37.7849,
                    'longitude' => -122.4094
                ],
                [
                    'user' => 'Alice Johnson',
                    'problem' => 'Leaking pipe',
                    'date' => '2025-08-18',
                    'location' => '3rd Blvd, Cityville',
                    'latitude' => 37.7649,
                    'longitude' => -122.4294
                ],
            ]
        ];
        return view('admin/Reports', $data);
    }
    
}
