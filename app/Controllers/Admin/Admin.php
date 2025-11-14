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
         $this->db = \Config\Database::connect(); //Connection to the database? I dont know man...
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

    /**
     * Load dashboard content with statistics (AJAX)
     * @return string
     */
    public function content()
    {   
        try {
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
        $pending = $this->usersModel->where('status', 'Pending')->countAllResults();
        $inactive = $this->usersModel->where('status', 'Inactive')->countAllResults();

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


    // Filter users based on search, purok, and status - AJAX
   public function filterUsers()
    {
        // Get filter parameters from query string
        $search = $this->request->getGet('search');
        $purok  = $this->request->getGet('purok');
        $status = $this->request->getGet('status');

        // Build query with user and user_information tables
        $builder = $this->db->table('users u')
            ->select('u.id, u.email, u.status, ui.first_name, ui.last_name, ui.purok')
            ->join('user_information ui', 'u.id = ui.user_id', 'left');

        // Apply search filter (searches name and email)
        if (!empty($search)) {
            $builder->groupStart()
                ->like('ui.first_name', $search)
                ->orLike('ui.last_name', $search)
                ->orLike('u.email', $search)
                ->groupEnd();
        }

        // Apply purok filter
        if (!empty($purok)) {
            $builder->where('ui.purok', $purok);
        }

        // Apply status filter
        if (!empty($status)) {
            $builder->where('u.status', $status);
        }

        // Execute query and get results
        $users = $builder->get()->getResultArray();

        // Return JSON response
        return $this->response->setJSON($users);
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

    //Get user info for modal (AJAX) for Verify User and user details
    public function getUser($id)
    {
        $userModel = $this->usersModel;
        $userInfoModel = $this->userInfoModel;

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
                'profile_picture' => $info['profile_picture'] ?? '',
                'status'        => $user['status'] ?? '',
                'created_at'    =>$user['created_at'] ?? '',
                'is_verified'      =>$user['is_verified'] ?? ''
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
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Please login first.');
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return redirect()->back()->with('error', 'Admin profile not found.');
        }

        $data = [
            'admin' => $admin,
            'title' => 'Edit Profile'
        ];

        return view('admin/edit_profile', $data);
    }

    /**
     * Get admin profile info (AJAX endpoint)
     */
    public function getProfileInfo()
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Profile not found']);
        }

        // Remove sensitive data
        unset($admin['password']);
        unset($admin['otp_code']);
        unset($admin['otp_expire']);

        return $this->response->setJSON(['success' => true, 'data' => $admin]);
    }

    /**
     * Display reports page with dynamic data
     * @return string
     */
    public function reports()
    {
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-01-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $reportType = $this->request->getGet('report_type') ?? 'all';

        // Define rate structure
        $RATE_NORMAL = 60;
        $RATE_SENIOR = 48;
        $RATE_ALONE = 30;

        // Get all approved users with their information
        $totalHouseholds = $this->usersModel->where('status', 'Approved')->countAllResults();
        
        // Count users by type (using age >= 60 for seniors, family_number = 1 for living alone)
        $seniorCount = $this->db->table('users')
            ->join('user_information', 'users.id = user_information.user_id')
            ->where('users.status', 'Approved')
            ->where('user_information.age >=', 60)
            ->countAllResults();
            
        $aloneCount = $this->db->table('users')
            ->join('user_information', 'users.id = user_information.user_id')
            ->where('users.status', 'Approved')
            ->where('user_information.family_number', 1)
            ->where('user_information.age <', 60) // Not senior
            ->countAllResults();
            
        $normalCount = $totalHouseholds - $seniorCount - $aloneCount;
        
        // Calculate expected monthly collection based on user types
        $monthlyExpected = ($normalCount * $RATE_NORMAL) + ($seniorCount * $RATE_SENIOR) + ($aloneCount * $RATE_ALONE);
        $averageRate = $totalHouseholds > 0 ? round($monthlyExpected / $totalHouseholds, 2) : 0;
        
        // Get actual collection for current month
        $currentMonthCollected = $this->billingModel
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('MONTH(updated_at)', date('m'))
            ->where('YEAR(updated_at)', date('Y'))
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0;

        // Get paid households count for current month
        $paidHouseholds = $this->billingModel
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('MONTH(due_date)', date('m'))
            ->where('YEAR(due_date)', date('Y'))
            ->countAllResults();

        // Pending payments
        $pendingCount = $this->billingModel
            ->where('status', 'Pending')
            ->where('MONTH(due_date)', date('m'))
            ->where('YEAR(due_date)', date('Y'))
            ->countAllResults();

        // Calculate pending amount (approximate using average rate)
        $pendingAmount = $pendingCount * $averageRate;

        // Calculate collection rate
        $collectionRate = $totalHouseholds > 0 ? round(($paidHouseholds / $totalHouseholds) * 100, 1) : 0;

        // Get monthly data for chart
        $monthlyData = $this->billingModel
            ->select("MONTH(updated_at) as month, COUNT(*) as paid_count, SUM(amount_due) as total_amount")
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('YEAR(updated_at)', date('Y'))
            ->groupBy('MONTH(updated_at)')
            ->orderBy('MONTH(updated_at)', 'ASC')
            ->get()
            ->getResultArray();

        // Format monthly collection data
        $collectionRates = array_fill(0, 12, 0);
        $collectionAmounts = array_fill(0, 12, 0);
        
        foreach ($monthlyData as $data) {
            $monthIndex = (int)$data['month'] - 1;
            $collectionAmounts[$monthIndex] = (float)$data['total_amount'];
            $collectionRates[$monthIndex] = $totalHouseholds > 0 ? round(((int)$data['paid_count'] / $totalHouseholds) * 100, 1) : 0;
        }

        // Get payment status breakdown
        $latePayments = $this->billingModel
            ->where('status', 'Pending')
            ->where('due_date <', date('Y-m-d'))
            ->countAllResults();

        $data = [
            'title' => 'Fixed-Rate Water Bill Reports',
            'rateNormal' => $RATE_NORMAL,
            'rateSenior' => $RATE_SENIOR,
            'rateAlone' => $RATE_ALONE,
            'averageRate' => $averageRate,
            'normalCount' => $normalCount,
            'seniorCount' => $seniorCount,
            'aloneCount' => $aloneCount,
            'totalHouseholds' => $totalHouseholds,
            'monthlyExpected' => $monthlyExpected,
            'currentMonthCollected' => $currentMonthCollected,
            'paidHouseholds' => $paidHouseholds,
            'pendingCount' => $pendingCount,
            'pendingAmount' => $pendingAmount,
            'collectionRate' => $collectionRate,
            'latePayments' => $latePayments,
            'annualTotal' => $monthlyExpected * 12,
            'collectionRates' => json_encode($collectionRates),
            'collectionAmounts' => json_encode($collectionAmounts),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => $reportType
        ];

        return view('admin/reports', $data);
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

    /* //View single user details
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
    */

    /* // Toggle user active status
    public function toggleUserStatus($id)
    {
        $user = $this->usersModel->find($id);
        if (!$user) return redirect()->back()->with('error', 'User not found.');

        $newStatus = $user['active'] ? 0 : 1;
        $this->usersModel->update($id, ['active' => $newStatus]);

        return redirect()->back()->with('success', 'User status updated.');
    }
    */

    /**
     * Add a new user account (AJAX)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function addUser()
    {
        // Validate input
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
            'purok'      => 'required|in_list[1,2,3,4,5]',
            'status'     => 'required|in_list[pending,approved,suspended,rejected]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert into users table
            $userData = [
                'email'      => $this->request->getPost('email'),
                'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'status'     => $this->request->getPost('status'),
                'active'     => $this->request->getPost('status') === 'approved' ? 2 : 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->usersModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Insert into user_information table
            $userInfoData = [
                'user_id'    => $userId,
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'purok'      => $this->request->getPost('purok'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->userInfoModel->insert($userInfoData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaction failed'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User account created successfully',
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Add User Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Manage user accounts with billing info
     * @return string
     */
    public function manageAccounts()
    {
        $status = $this->request->getGet('status') ?? 'all';
        $search = $this->request->getGet('search') ?? '';

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
            'payment' => $this->paymentsModel->first(),
        ]);
    }

    //Display announcements page
    public function announcements()
    {
        return view('admin/announcements', ['title' => 'Announcements']);
    }



   /*  //Display payment settings
    public function paymentSettings()
    {
        $data = [
            'title' => 'Payment Settings',
            'payment' => $this->paymentModel->first(),
        ];

        return view('admin/paymentSettings', $data);
    } */

    //Update QR code and payment details

    /* public function updateQR()
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
    */
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
        if (!$adminId) {
            return $this->response->setJSON(['success' => false, 'message' => 'You must be logged in.']);
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Admin not found.']);
        }

        // Validation rules
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name'  => 'required|min_length[2]|max_length[50]',
            'email'      => 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $updateData = [
            'first_name'  => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name'   => $this->request->getPost('last_name'),
            'email'       => $this->request->getPost('email'),
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

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data'    => $updateData
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update profile. Please try again.'
        ]);
    }
}
?>