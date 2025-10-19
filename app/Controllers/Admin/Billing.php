<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\UsersModel;

class Billing extends BaseController
{
    /**
     * Show a single billing record
     */
    public function view($id)
    {
        $billingModel = new BillingModel();

        $bill = $billingModel
            ->select('billings.*, 
                      CONCAT(user_information.first_name, " ", user_information.last_name) AS user_name, 
                      users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
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
                      CONCAT(user_information.first_name, " ", user_information.last_name) AS user_name, 
                      users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
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

        // ✅ Generate unique bill number
        $billNo = 'BILL-' . strtoupper(uniqid());

        $data = [
            'bill_no'    => $billNo, // ✅ Add this line
            'user_id'    => $this->request->getPost('user_id'),
            'amount'     => $this->request->getPost('amount') ?? 60,
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
