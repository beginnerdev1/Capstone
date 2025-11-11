<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\PaymentsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Billing extends BaseController
{
    protected $usersModel;
    protected $userInfoModel;
    protected $billingModel;
    protected $paymentsModel;
    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->userInfoModel = new UserInformationModel();
        $this->billingModel = new BillingModel();
        $this->paymentsModel = new PaymentsModel();
    }

    /**
     * View a single billing record by ID
     */
    public function view($id)
    {
        $bill = $this->billingModel
        ->select('billings.*, 
                CONCAT(user_information.first_name, " ", user_information.last_name) AS user_name, 
                users.email,
                users.status')
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
            ->whereIn('billings.status', ['Paid', 'Over the Counter'])
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
        $amount = $this->p('amount');
        $due_date = $this->p('due_date');

        // Validate inputs
        if (empty($amount) || empty($due_date)) {
            return redirect()->back()->with('error', 'Amount and due date are required.');
        }

        // Get the user
        $user = $this->usersModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Block Pending users
        if ($user['status'] === 'pending') {
            return redirect()->back()->with('error', 'Cannot add bills for pending users.');
        }

        // Check if there is already a bill for this user in the same month/year
        $existing = $this->billingModel
            ->where('user_id', $userId)
            ->where('MONTH(due_date)', date('m', strtotime($due_date)))
            ->where('YEAR(due_date)', date('Y', strtotime($due_date)))
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'A bill for this month already exists for this user.');
        }

        // Generate unique bill number
        $bill_no = 'BILL-' . date('YmdHis') . '-' . rand(100, 999);

        // Prepare data
        $data = [
            'user_id'     => $userId,
            'bill_no'     => $bill_no,
            'amount_due'  => $amount,
            'due_date'    => $due_date,
            'status'      => 'Pending',
            'created_at'  => date('Y-m-d H:i:s')
        ];

        // Insert billing record
        if ($this->billingModel->insert($data)) {
            return redirect()->back()->with('success', 'New bill added successfully!');
        }

        return redirect()->back()->with('error', 'Failed to add bill. Please try again.');
    }
    /**
     * Edit an existing billing record
     */
    public function editBill($id)
    {
        $bill = $this->billingModel->find($id);
        if (!$bill) {
            return redirect()->back()->with('error', 'Bill not found.');
        }

        $amount = $this->request->getPost('amount');
        $dueDate = $this->request->getPost('due_date');

        // Optional: enforce one bill per month again
        $existing = $this->billingModel
            ->where('user_id', $bill['user_id'])
            ->where('MONTH(due_date)', date('m', strtotime($dueDate)))
            ->where('YEAR(due_date)', date('Y', strtotime($dueDate)))
            ->where('id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'A bill for this month already exists.');
        }

        $this->billingModel->update($id, [
            'amount_due' => $amount,
            'due_date'   => $dueDate,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Bill updated successfully.');
    }

    /**
     * Update billing status
     */
   public function updateStatus($id)
    {
        $status = $this->p('status', ['Paid', 'Pending', 'Over the Counter']);

        if (!$status) {
            return redirect()->back()->with('error', 'Invalid or missing status.');
        }

        // Get billing record
        $billing = $this->billingModel->find($id);
        if (!$billing) {
            return redirect()->back()->with('error', 'Billing record not found.');
        }

        // 🧩 Prepare update data
        $updateData = ['status' => $status];

        // ✅ Automatically set/clear paid_date
        if (in_array($status, ['Paid', 'Over the Counter'])) {
            $updateData['paid_date'] = date('Y-m-d H:i:s');
        } else {
            $updateData['paid_date'] = null;
        }

        // 🔁 Update the billing status + paid_date
        if ($this->billingModel->update($id, $updateData)) {

            // ✅ If marked as "Over the Counter", also create a payment record
            if ($status === 'Over the Counter') {
                $paymentData = [
                    'billing_id'        => $billing['id'],
                    'user_id'           => $billing['user_id'],
                    'amount'            => $billing['amount_due'],
                    'method'            => 'offline',
                    'status'            => 'over the counter',
                    'reference_number'  => 'OTC-' . strtoupper(uniqid()),
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];

                $this->paymentsModel->insert($paymentData);
            }

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
?>