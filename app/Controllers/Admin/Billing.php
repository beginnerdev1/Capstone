<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Billing extends BaseController
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

    /**
     * View a single billing record by ID
     */
    public function view($id)
    {
        $bill = $this->billingModel
            ->select('billings.*, 
                      CONCAT(user_information.first_name, " ", user_information.last_name) AS user_name, 
                      users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->where('billings.id', $id)
            ->first();

        if (!$bill) {
            throw new PageNotFoundException("Bill ID {$id} not found.");
        }

        return view('admin/billing_view', [
            'title' => 'View Billing',
            'bill'  => $bill
        ]);
    }

    /**
     * Show all paid bills
     */
    public function paidBills()
    {
        $bills = $this->billingModel
            ->select('billings.*, 
                      CONCAT(user_information.first_name, " ", user_information.last_name) AS user_name, 
                      users.email')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->whereIn('billings.status', ['Paid', 'Over the Counter']) // ✅ include OTC
            ->orderBy('billings.updated_at', 'DESC')
            ->findAll();

        return view('admin/paidBills', [
            'title' => 'Paid Bills',
            'bills' => $bills
        ]);
    }

    /**
     * Add a new billing record
     */
    public function addBill($userId)
    {
        $amount = $this->request->getPost('amount');
        $due_date = $this->request->getPost('due_date');

        // Validation check
        if (empty($amount) || empty($due_date)) {
            return redirect()->back()->with('error', 'Amount and due date are required.');
        }

        // Generate unique bill number
        $bill_no = 'BILL-' . date('YmdHis') . '-' . rand(100, 999);

        $data = [
            'user_id'     => $userId,
            'bill_no'     => $bill_no,
            'amount_due'  => $amount,
            'due_date'    => $due_date,
            'status'      => 'Pending',
            'created_at'  => date('Y-m-d H:i:s')
        ];

        if ($this->billingModel->insert($data)) {
            return redirect()->back()->with('success', 'New bill added successfully!');
        }

        return redirect()->back()->with('error', 'Failed to add bill. Please try again.');
    }

    /**
     * Update billing status (Paid / Over the Counter)
     */
    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');

        if (!$status) {
            return redirect()->back()->with('error', 'No status provided.');
        }

        if ($this->billingModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', "Billing status updated to '{$status}' successfully.");
        }

        return redirect()->back()->with('error', 'Failed to update billing status.');
    }

    /**
     * Delete a billing record
     */
    public function delete($id)
    {
        if ($this->billingModel->delete($id)) {
            return redirect()->back()->with('success', 'Billing deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete billing.');
    }
}
