<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\UserModel;

class Billing extends BaseController
{
    /**
     * Display all billings (with user info)
     */
    public function index()
    {
        $billingModel = new BillingModel();
        $userModel    = new UserModel();

        // Users for the "create bill" dropdown
        $users = $userModel->select('id, Firstname, Surname')->orderBy('Surname', 'ASC')->findAll();

        // Status options for the filter dropdown (adjust labels to match your DB values)
        $statuses = ['All', 'Pending', 'Paid', 'Rejected', 'Over the Counter', 'Unpaid', 'Overdue'];

        // Selected filter from query string
        $selectedStatus = $this->request->getGet('status');

        // Build query for bills with joined user name
        $builder = $billingModel
            ->select('billings.*, CONCAT(users.Firstname, " ", users.Surname) AS user_name, users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->orderBy('billings.created_at', 'DESC');

        // Apply status filter only if provided and not 'All'
        if ($selectedStatus && $selectedStatus !== 'All') {
            $builder->where('billings.status', $selectedStatus);
        }

        $bills = $builder->findAll();

        return view('admin/Billings', [
            'title' => 'Billing Management',
            'bills' => $bills,
            'users' => $users,
            'statuses' => $statuses,
            'selectedStatus' => $selectedStatus
        ]);
    }

    /**
     * Show a single billing record
     */
    public function view($id)
    {
        $billingModel = new BillingModel();

        $bill = $billingModel
            ->select('billings.*, 
                      CONCAT(users.Firstname, " ", users.Surname) AS user_name, 
                      users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->where('billings.id', $id)
            ->first();

        if (!$bill) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bill ID {$id} not found.");
        }

        return view('admin/billing_view', [
            'title' => 'View Billing',
            'bill'  => $bill
        ]);
    }

    /**
     * Show only paid bills
     */
    public function paidBills()
    {
        $billingModel = new BillingModel();

        $bills = $billingModel
            ->select('billings.*, 
                      CONCAT(users.Firstname, " ", users.Surname) AS user_name, 
                      users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->where('billings.status', 'Paid')
            ->orderBy('billings.updated_at', 'DESC')
            ->findAll();

        return view('admin/paidBills', [
            'title' => 'Paid Bills',
            'bills' => $bills
        ]);
    }

    /**
     * Create a new billing record
     */
    public function create()
    {
        $billingModel = new BillingModel();

        $data = [
            'user_id'    => $this->request->getPost('user_id'),
            'amount'     => $this->request->getPost('amount') ?? 60, // default ₱60
            'due_date'   => $this->request->getPost('due_date'),
            'status'     => 'Unpaid',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($billingModel->insert($data)) {
            return redirect()->to('/admin/billings')->with('success', 'Billing added successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create billing.');
    }

    /**
     * Update a bill's payment status
     */
    public function updateStatus($id)
    {
        $billingModel = new BillingModel();
        $status = $this->request->getPost('status');

        if ($billingModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Billing status updated.');
        }

        return redirect()->back()->with('error', 'Failed to update billing status.');
    }

    /**
     * Delete a billing record
     */
    public function delete($id)
    {
        $billingModel = new BillingModel();

        if ($billingModel->delete($id)) {
            return redirect()->back()->with('success', 'Billing deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete billing.');
    }
}
