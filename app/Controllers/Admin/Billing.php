<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;

class Billing extends BaseController
{
    /**  para hindi an mag: $NameNgModel = new NameNgModel(); sa bawat function
     *  pero ganto nalang this->NameNgModel
     * Mula dito
    */
    protected $usersModel;
    protected $userInfoModel;
    protected $billingModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->userInfoModel = new UserInformationModel();
        $this->billingModel = new BillingModel();
    }
    // hanggang dito

      //Show a single billing record
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

    //Show only paid bills
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

    //Create a new billing record
   public function addBill($userId)
    {
        $amount = $this->request->getPost('amount');
        $due_date = $this->request->getPost('due_date');

        //  Generate unique bill number (timestamp + random)
        $bill_no = 'BILL-' . date('YmdHis') . '-' . rand(100, 999);

        $data = [
            'user_id' => $userId,
            'bill_no' => $bill_no,
            'amount_due' => $amount,
            'due_date' => $due_date,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->billingModel->insert($data)) {
            return redirect()->back()->with('success', 'New bill added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add bill. Please try again.');
        }
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
