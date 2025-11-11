<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\GcashSettingsModel;
use App\Models\PaymentsModel; // plural, renamed

class Admin extends BaseController
{
    protected $usersModel;
    protected $userInfoModel;
    protected $billingModel;
    protected $adminModel;
    protected $paymentsModel; // match property name

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->userInfoModel = new UserInformationModel();
        $this->billingModel = new BillingModel();
        $this->adminModel = new AdminModel();
         $this->paymentsModel = new PaymentsModel();
    }
    /**
     * Display admin dashboard
     */
    public function index()
    { 
            return view('admin/Dashboard');
    }

    public function content()
    {   try {
 
        // === Billing data ===
        $row = $this->billingModel
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('YEAR(updated_at)', date('Y'))
            ->selectSum('amount_due')
            ->get()
            ->getRow();

        $totalCollected = $row ? $row->amount_due : 0;
        $unpaidCount = $this->billingModel
            ->where('status', 'Pending')
            ->countAllResults();

        $row = $this->billingModel
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('MONTH(updated_at)', date('m'))
            ->where('YEAR(updated_at)', date('Y'))
            ->selectSum('amount_due')
            ->get()->getRow();

        $monthlyTotal = $row ? $row->amount_due : 0;

       
        // === User counts ===
        $active = $this->usersModel->where('status', 'Approved')->countAllResults();
        $pending = $this->usersModel->where('status', 'ending')->countAllResults();
        $inactive = $this->usersModel->where('status', 'inactive')->countAllResults();

        // === Monthly income data (for charts) ===
        $query = $this->billingModel->select("
                DATE_FORMAT(updated_at, '%b') AS month,
                SUM(amount_due) AS total
            ")
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('YEAR(updated_at)', date('Y'))
            ->groupBy('MONTH(updated_at)')
            ->orderBy('MONTH(updated_at)', 'ASC')
            ->get();

        $allMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $monthlyTotals = array_fill(0, 12, 0); // default 0 for all months

        foreach ($query->getResultArray() as $row) {
            $monthName = $row['month'];
            $total = (float) $row['total'];
            $index = array_search($monthName, $allMonths);
            if ($index !== false) {
                $monthlyTotals[$index] = $total;
            }
        }

        // === Pass data to view ===
        $data = [
            'months' => json_encode($allMonths),
            'incomeData' => json_encode($monthlyTotals),
            'totalCollected' => $totalCollected,
            'monthlyTotal' => $monthlyTotal,
            'active' => $active,
            'pending' => $pending,
            'inactive' => $inactive,
        ];
         } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

        return view('admin/dashboard-content', $data);
    }

    public function layoutStatic() { return view('admin/layout-static'); }
    public function charts() { return view('admin/charts'); }
    public function page404() { return view('admin/404'); }
    public function page401() { return view('admin/401'); }
    public function page500() { return view('admin/500'); }
    public function tables() { return view('admin/tables'); }


// ======================================================
// 💳 USER MANAGEMENT
// ======================================================

// Show Registered Users - Ajax Done
    public function registeredUsers()
    {
        $users = $this->usersModel->getAllUsersWithInfo();

        $data = [
            'users' => $users,
            'puroks' => ['1','2','3','4','5'],
            'selectedPurok' => null,
            'search' => null
        ];

        return view('admin/registeredUsers', $data);
    }

    // Filter users based on search and purok - AJAX
    public function filterUsers()
    {
        $search = $this->request->getVar('search');
        $purok = $this->request->getVar('purok');

        $builder = $this->usersModel
                        ->select('users.*, user_information.first_name, user_information.last_name, user_information.purok')
                        ->join('user_information', 'user_information.user_id = users.id', 'left');

        if($search) {
            $builder->groupStart()
                    ->like('users.email', $search)
                    ->orLike('user_information.first_name', $search)
                    ->orLike('user_information.last_name', $search)
                    ->orLike('users.id', $search)
                    ->groupEnd();
        }

        if($purok) {
            $builder->where('user_information.purok', $purok);
        }

        $users = $builder->findAll();

        $statusMap = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'suspended' => 'Suspended',
            'inactive' => 'Inactive'
        ];

        $result = [];
        foreach($users as $user) {
            $result[] = [
                'id' => $user['id'],
                'name' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
                'purok' => $user['purok'] ?? 'N/A',
                'email' => $user['email'],
                'status' => $statusMap[$user['status']] ?? 'Unknown'
            ];
        }

        return $this->response->setJSON($result);
    }

//Display  VERIFY USER  accounts for approval/rejection
public function pendingAccounts()
{
    $usersModel = new \App\Models\UsersModel();
    $users = $usersModel
        ->select('users.id, users.email, users.status, user_information.first_name, user_information.last_name, user_information.purok, user_information.barangay')
        ->join('user_information', 'user_information.user_id = users.id', 'left')
        ->where('users.status', 'Pending')
        ->orderBy('users.created_at', 'DESC')
        ->findAll();

    // Return JSON if Ajax
    if ($this->request->getGet('ajax')) {
        return $this->response->setJSON($users);
    }

    return view('admin/pendingAccounts', ['users' => $users]);
}

//Get user info for modal (AJAX) for Verify User
public function getUser($id)
{
    $userModel = new \App\Models\UsersModel();
    $userInfoModel = new \App\Models\UserInformationModel();

    $user = $userModel->find($id);
    $info = $userInfoModel->getByUserId($id);

    if (!$user) return $this->response->setJSON([]);

    return $this->response->setJSON([
        'id'            => $user['id'],
        'email'         => $user['email'],
        'first_name'    => $info['first_name'] ?? '',
        'last_name'     => $info['last_name'] ?? '',
        'gender'        => $info['gender'] ?? '',
        'age'           => $info['age'] ?? '',
        'family_number' => $info['family_number'] ?? '',
        'phone'         => $info['phone'] ?? '',
        'purok'         => $info['purok'] ?? '',
        'barangay'      => $info['barangay'] ?? '',
        'municipality'  => $info['municipality'] ?? '',
        'province'      => $info['province'] ?? '',
        'zipcode'       => $info['zipcode'] ?? '',
        'profile_picture' => $info['profile_picture'] ?? ''
    ]);
}


// ======================================================
// 💳 UTILITIES
// ======================================================


//Display GCash settings page
public function gcashsettings() {
        return view('admin/gcash_settings');
}

// Save GCash settings
public function saveGcashSettings()
{
    try {
        // Ensure request is POST
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Load model
        $model = new \App\Models\GcashSettingsModel();

        // Validation rules
        $rules = [
            'gcash_number' => 'required|regex_match[/^[0-9]{11}$/]',
            'gcash_qr'     => 'max_size[gcash_qr,5000]|is_image[gcash_qr]|mime_in[gcash_qr,image/png,image/jpg,image/jpeg]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validator->getErrors()
            ]);
        }

        $settings = $model->find(1);
        $file = $this->request->getFile('gcash_qr');
        $qrCodePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/qrcodes';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old QR code if exists
            if ($settings && !empty($settings['qr_code_path']) && file_exists(FCPATH . $settings['qr_code_path'])) {
                unlink(FCPATH . $settings['qr_code_path']);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $qrCodePath = 'uploads/qrcodes/' . $newName;
        }

        $gcashNumber = $this->request->getPost('gcash_number');

        $updateData = ['gcash_number' => $gcashNumber];
        if ($qrCodePath) {
            $updateData['qr_code_path'] = $qrCodePath;
        }

        if (!$settings) {
            $model->insert($updateData);
        } else {
            $model->update(1, $updateData);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Settings saved successfully']);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

// ---------------- Transaction Records ----------------

// Display transaction records page
public function transactionRecords()
{
    $data = [
        'current_month' => date('Y-m-d') // today
    ];
    return view('admin/transaction_records', $data);
}

// ---------------- Payments Functions ----------------

// Display monthly payments page
public function monthlyPayments()
{
    $month = $this->request->getGet('month') ?? date('Y-m');
    $method = $this->request->getGet('method') ?? '';
    $search = $this->request->getGet('search') ?? '';

    $filters = [
        'month' => $month,
        'method' => $method,
        'search' => $search
    ];

    // Use the renamed model
    $payments = $this->paymentsModel->getMonthlyPayments($filters);
    $stats = $this->paymentsModel->getMonthlyStats($filters);

    $formattedPayments = [];
    foreach ($payments as $payment) {
        $formattedPayments[] = [
            'id' => $payment['id'],
            'user_name' => $payment['user_name'] ?? 'Unknown',
            'email' => $payment['email'] ?? 'N/A',
            'amount' => $payment['amount'] ?? 0,
            'method' => $payment['method'] ?? 'Unknown',
            'status' => $payment['status'] ?? 'Pending',
            'reference' => $payment['reference_number'] ?? 'N/A',
            'admin_reference' => $payment['admin_reference'] ?? '',
            'receipt' => $payment['receipt_image'] ? base_url($payment['receipt_image']) : null,
            'created_at' => $payment['created_at'] ?? date('Y-m-d H:i:s'),
            'paid_at' => $payment['paid_at'] ?? null,
            'avatar' => $payment['avatar'] ?? 'NA'
        ];
    }

    $data = [
        'title' => 'Monthly Payments',
        'payments' => $formattedPayments,
        'stats' => $stats,
        'current_month' => $month,
        'filters' => $filters
    ];

    return view('admin/monthly_payments', $data);
}

// Fetch payments data for AJAX
    public function getPaymentsData()
    {
        $month = $this->request->getGet('month');
        $method = $this->request->getGet('method');
        $search = $this->request->getGet('search');

        $filters = [
            'month' => $month,
            'method' => $method,
            'search' => $search
        ];

        $payments = $this->paymentsModel->getMonthlyPayments($filters);
        $stats = $this->paymentsModel->getMonthlyStats($filters);

        return $this->response->setJSON([
            'success' => true,
            'payments' => $payments,
            'stats' => $stats
        ]);
    }


    // Confirm GCash payment (AJAX)
  public function confirmGCashPayment()
    {
        $data = $this->request->getJSON(true);
        $paymentId = $data['payment_id'] ?? null;
        $adminRef = $data['admin_reference'] ?? null;

        if (!$paymentId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Payment ID is required']);
        }

        $updated = $this->paymentsModel->confirmGCashPayment($paymentId, $adminRef);

        return $this->response->setJSON([
            'success' => (bool)$updated,
            'message' => $updated ? 'Payment confirmed successfully' : 'Failed to confirm payment'
        ]);
    }

public function exportPayments()
{
    $month = $this->request->getGet('month') ?? date('Y-m');
    $method = $this->request->getGet('method') ?? '';
    $search = $this->request->getGet('search') ?? '';

    $filters = [
        'month' => $month,
        'method' => $method,
        'search' => $search
    ];

    $payments = $this->paymentModel->getMonthlyPayments($filters);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payments_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'Amount', 'Method', 'Status', 'Reference', 'Admin Reference', 'Date']);

    foreach ($payments as $payment) {
        fputcsv($output, [
            $payment['user_name'] ?? 'Unknown',
            $payment['email'] ?? 'N/A',
            number_format($payment['amount'] ?? 0, 2),
            $payment['method'] ?? 'Unknown',
            $payment['status'] ?? 'Pending',
            $payment['reference_number'] ?? 'N/A',
            $payment['admin_reference'] ?? 'N/A',
            date('Y-m-d', strtotime($payment['created_at'] ?? 'now'))
        ]);
    }

    fclose($output);
    exit;
}











// Edit user/admin profile
public function editProfile()
{
    return view('admin/edit_profile'); // Page to edit user/admin profile
}

// Display reports page
public function reports()
{
    return view('admin/reports'); // Loads the reports.php view
}






    //Activate a user (status = 2)
    public function activateUser($id)
    {
        $this->usersModel->update($id, ['active' => 2,'status' => 'approved']);
        return redirect()->back()->with('success', 'User activated successfully.');
    }


    // Deactivate a user (status = 1)
    public function deactivateUser($id)
    {
        $this->usersModel->update($id, ['active' => 1, 'status' => 'inactive']);
        return redirect()->back()->with('success', 'User deactivated successfully.');
    }

    //Suspend a user (status = -1)
    public function suspendUser($id)
    {
        $this->usersModel->update($id, [
    'active' => -1, 
    'status' => 'suspended'
    ]);;
        return redirect()->back()->with('success', 'User suspended successfully.');
    }

    // Approve a pending user
    public function approve($id)
    {
        if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

        if ($this->usersModel->update($id, ['status' => 'approved', 'active' => 2])) {
            return redirect()->back()->with('success', 'User approved successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve user.');
    }

    // Reject a pending user
    public function reject($id)
    {
        if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

        if ($this->usersModel->update($id, ['status' => 'rejected'])) {
            return redirect()->back()->with('success', 'User rejected successfully.');
        }

        return redirect()->back()->with('error', 'Failed to reject user.');
    }

    //View single user details
    public function viewUser($id)
    {
        $user = $this->usersModel
            ->select('users.*, user_information.*')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->where('users.id', $id)
            ->first();

        if (!$user) {
            return redirect()->to('/admin/registeredUsers')->with('error', 'User not found');
        }

        return view('admin/viewUser', [
            'title' => 'View User Details',
            'user'  => $user
        ]);
    }

    // Toggle user active status
    public function toggleUserStatus($id)
    {
        $user = $this->usersModel->find($id);
        if (!$user) return redirect()->back()->with('error', 'User not found.');

        $newStatus = $user['active'] ? 0 : 1;
        $this->usersModel->update($id, ['active' => $newStatus]);

        return redirect()->back()->with('success', 'User status updated.');
    }

    //Manage user accounts with billing info
    public function manageAccounts()
    {
        $status = $this->g('status', ['Pending', 'Paid', 'Rejected', 'Over the Counter', 'All'], 'all');
        $search = $this->g('search', null, '');

        $users = $this->usersModel
        ->select('users.id, users.email, users.status, users.is_verified, user_information.first_name, user_information.last_name, user_information.phone, user_information.barangay, user_information.purok')
        ->join('user_information', 'user_information.user_id = users.id', 'left');
        if (!empty($search)) {
            $users->groupStart()
                ->like('user_information.first_name', $search)
                ->orLike('user_information.last_name', $search)
                ->groupEnd();
        }

        $users = $users->findAll();

        foreach ($users as &$user) {
            $builder = $this->billingModel->where('user_id', $user['id']);

            if (!empty($status) && strtolower($status) != 'all') {
                if ($status == 'Paid') {
                    $builder->groupStart()
                        ->where('status', 'Paid')
                        ->orWhere('status', 'Over the Counter')
                        ->groupEnd();
                } else {
                    $builder->where('status', $status);
                }
            }

            $user['unpaid_bills'] = $builder->findAll();
        }

        if (!empty($status) && strtolower($status) != 'all') {
            $users = array_filter($users, fn($u) => !empty($u['unpaid_bills']));
        }

        return view('admin/manageAccounts', [
            'users' => $users,
            'search' => $search,
            'selectedStatus' => $status,
            'payment' => $this->paymentModel->first(),
        ]);
    }

    //Display announcements page
    public function announcements()
    {
        return view('admin/announcements', ['title' => 'Announcements']);
    }



    //Display payment settings
    public function paymentSettings()
    {
        $data = [
            'title' => 'Payment Settings',
            'payment' => $this->paymentModel->first(),
        ];

        return view('admin/paymentSettings', $data);
    }

    //Update QR code and payment details

    public function updateQR()
    {
        $file = $this->request->getFile('qr_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/qr/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $data = [
                'payment_method' => $this->p('payment_method'),
                'account_name' => $this->p('account_name'),
                'account_number' => $this->p('account_number'),
                'qr_image' => 'uploads/qr/' . $newName,
            ];

            $this->paymentModel->update(1, $data);
            return redirect()->to('admin/manageAccounts')->with('success', 'QR updated successfully!');
        }

        return redirect()->back()->with('error', 'QR upload failed!');
    }

    //Display admin profile
    public function profile()
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) return redirect()->to(base_url('admin/login'))->with('error', 'You must be logged in.');

        $admin = $this->adminModel->find($adminId);
        if (!$admin) return redirect()->to(base_url('admin/login'))->with('error', 'Admin not found.');

        return view('admin/profile', ['admin' => $admin]);
    }

    //Update admin profile
    public function updateProfile()
    {
        helper(['form', 'url']);

        $adminId = session()->get('admin_id');
        if (!$adminId) return redirect()->to(base_url('admin/login'))->with('error', 'You must be logged in.');

        $admin = $this->adminModel->find($adminId);
        if (!$admin) return redirect()->back()->with('error', 'Admin not found.');

        $updateData = [
            'first_name'  => $this->p('first_name'),
            'middle_name' => $this->p('middle_name'),
            'last_name'   => $this->p('last_name'),
            'email'       => $this->p('email'),
        ];

        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/profile/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

            $newName = $file->getRandomName();
            if ($file->move($uploadPath, $newName)) {
                if (!empty($admin['profile_picture']) && $admin['profile_picture'] !== 'default.png') {
                    $oldFile = $uploadPath . $admin['profile_picture'];
                    if (file_exists($oldFile)) unlink($oldFile);
                }
                $updateData['profile_picture'] = $newName;
            }
        }

        if ($this->adminModel->update($adminId, $updateData)) {
            session()->set([
                'admin_first_name'  => $updateData['first_name'],
                'admin_middle_name' => $updateData['middle_name'],
                'admin_last_name'   => $updateData['last_name'],
                'admin_email'       => $updateData['email'],
                'admin_picture'     => $updateData['profile_picture'] ?? $admin['profile_picture'] ?? 'default.png',
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }
}
?>