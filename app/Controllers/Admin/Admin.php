<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\GcashSettingsModel;
use App\Models\PaymentsModel;

class Admin extends BaseController
{
    protected $usersModel;
    protected $userInfoModel;
    protected $billingModel;
    protected $adminModel;
    protected $paymentsModel;

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

    /**
     * Load dashboard content with statistics (AJAX)
     * Cached for 5 minutes to improve performance
     * @return string
     */
    public function content()
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'dashboard_stats_' . date('Y-m-d-H') . '_' . floor(date('i') / 5);
        
        $data = $cache->get($cacheKey);
        
        if ($data === null) {
            try {
                // === Billing data (optimized with single query) ===
                $currentYear = date('Y');
                $currentMonth = date('m');
                
                // Get annual and monthly totals in one query
                $billingStats = $this->billingModel
                    ->select("SUM(amount_due) as annual_total,
                             SUM(CASE WHEN MONTH(updated_at) = {$currentMonth} THEN amount_due ELSE 0 END) as monthly_total")
                    ->whereIn('status', ['Paid', 'Over the Counter'])
                    ->where('YEAR(updated_at)', $currentYear)
                    ->get()
                    ->getRow();

                $totalCollected = $billingStats ? (float)$billingStats->annual_total : 0;
                $monthlyTotal = $billingStats ? (float)$billingStats->monthly_total : 0;

                // === User counts (optimized with single query) ===
                $userStats = $this->usersModel
                    ->select("SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as active,
                             SUM(CASE WHEN status = 'ending' THEN 1 ELSE 0 END) as pending,
                             SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive")
                    ->get()
                    ->getRow();

                $active = $userStats ? (int)$userStats->active : 0;
                $pending = $userStats ? (int)$userStats->pending : 0;
                $inactive = $userStats ? (int)$userStats->inactive : 0;

                // === Monthly income data (for charts) ===
                $query = $this->billingModel->select("
                        DATE_FORMAT(updated_at, '%b') AS month,
                        MONTH(updated_at) AS month_num,
                        SUM(amount_due) AS total
                    ")
                    ->whereIn('status', ['Paid', 'Over the Counter'])
                    ->where('YEAR(updated_at)', $currentYear)
                    ->groupBy('MONTH(updated_at)')
                    ->orderBy('MONTH(updated_at)', 'ASC')
                    ->get();

                $allMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                $monthlyTotals = array_fill(0, 12, 0);

                foreach ($query->getResultArray() as $row) {
                    $monthIndex = (int)$row['month_num'] - 1;
                    $monthlyTotals[$monthIndex] = (float)$row['total'];
                }

                // === Revenue breakdown by rate category ===
                // Normal Rate: ₱60 (default users)
                // Senior Citizen Rate: ₱48 (age >= 60)
                // Living Alone Rate: ₱30 (family_number = 1)
                $revenueQuery = $this->billingModel
                    ->select("
                        SUM(CASE 
                            WHEN user_information.family_number = 1 THEN billings.amount_due 
                            ELSE 0 
                        END) as alone_revenue,
                        SUM(CASE 
                            WHEN user_information.age >= 60 AND user_information.family_number != 1 THEN billings.amount_due 
                            ELSE 0 
                        END) as senior_revenue,
                        SUM(CASE 
                            WHEN (user_information.age < 60 OR user_information.age IS NULL) AND user_information.family_number != 1 THEN billings.amount_due 
                            ELSE 0 
                        END) as normal_revenue
                    ")
                    ->join('user_information', 'user_information.user_id = billings.user_id', 'left')
                    ->whereIn('billings.status', ['Paid', 'Over the Counter'])
                    ->where('YEAR(billings.updated_at)', $currentYear)
                    ->get()
                    ->getRow();

                $aloneRevenue = $revenueQuery ? (float)$revenueQuery->alone_revenue : 0;
                $seniorRevenue = $revenueQuery ? (float)$revenueQuery->senior_revenue : 0;
                $normalRevenue = $revenueQuery ? (float)$revenueQuery->normal_revenue : 0;

                $data = [
                    'months' => json_encode($allMonths),
                    'incomeData' => json_encode($monthlyTotals),
                    'totalCollected' => $totalCollected,
                    'monthlyTotal' => $monthlyTotal,
                    'active' => $active,
                    'pending' => $pending,
                    'inactive' => $inactive,
                    'normalRevenue' => $normalRevenue,
                    'seniorRevenue' => $seniorRevenue,
                    'aloneRevenue' => $aloneRevenue,
                ];
                
                // Cache for 5 minutes
                $cache->save($cacheKey, $data, 300);
            } catch (\Exception $e) {
                log_message('error', 'Dashboard content error: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to load dashboard data']);
            }
        }

        return view('admin/dashboard-content', $data);
    }

    /**
     * Get dashboard statistics as JSON (optimized API endpoint)
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getDashboardStats()
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'dashboard_json_' . date('Y-m-d-H') . '_' . floor(date('i') / 5);
        
        $stats = $cache->get($cacheKey);
        
        if ($stats === null) {
            try {
                $currentYear = date('Y');
                $currentMonth = date('m');
                
                // Optimized billing stats
                $billingStats = $this->billingModel
                    ->select("SUM(amount_due) as annual_total,
                             SUM(CASE WHEN MONTH(updated_at) = {$currentMonth} THEN amount_due ELSE 0 END) as monthly_total")
                        ->where('status', 'Paid')
                    ->where('YEAR(updated_at)', $currentYear)
                    ->get()
                    ->getRow();

                // Optimized user stats
                $userStats = $this->usersModel
                    ->select("SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as active,
                             SUM(CASE WHEN status = 'ending' THEN 1 ELSE 0 END) as pending,
                             SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive")
                    ->get()
                    ->getRow();

                // Monthly chart data
                $monthlyData = $this->billingModel->select("
                        MONTH(updated_at) AS month_num,
                        SUM(amount_due) AS total
                    ")
                        ->where('status', 'Paid')
                    ->where('YEAR(updated_at)', $currentYear)
                    ->groupBy('MONTH(updated_at)')
                    ->orderBy('MONTH(updated_at)', 'ASC')
                    ->get()
                    ->getResultArray();

                $allMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                $monthlyTotals = array_fill(0, 12, 0);
                
                foreach ($monthlyData as $row) {
                    $monthIndex = (int)$row['month_num'] - 1;
                    $monthlyTotals[$monthIndex] = (float)$row['total'];
                }

                $stats = [
                    'success' => true,
                    'data' => [
                        'billing' => [
                            'annual' => $billingStats ? (float)$billingStats->annual_total : 0,
                            'monthly' => $billingStats ? (float)$billingStats->monthly_total : 0
                        ],
                        'users' => [
                            'active' => $userStats ? (int)$userStats->active : 0,
                            'pending' => $userStats ? (int)$userStats->pending : 0,
                            'inactive' => $userStats ? (int)$userStats->inactive : 0
                        ],
                        'chart' => [
                            'months' => $allMonths,
                            'income' => $monthlyTotals
                        ]
                    ],
                    'cached' => false,
                    'timestamp' => time()
                ];
                
                $cache->save($cacheKey, $stats, 300);
            } catch (\Exception $e) {
                log_message('error', 'Dashboard stats error: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'error' => 'Failed to fetch dashboard statistics'
                ]);
            }
        } else {
            $stats['cached'] = true;
        }

        return $this->response->setJSON($stats);
    }

    public function page404() { return view('admin/404'); }
    public function page401() { return view('admin/401'); }
    public function page500() { return view('admin/500'); }

    // ======================================================
    // 💳 USER MANAGEMENT
    // ======================================================

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

    public function filterUsers()
    {
        $search = $this->request->getVar('search');
        $purok = $this->request->getVar('purok');

        $builder = $this->usersModel
            ->select('users.*, user_information.first_name, user_information.last_name, user_information.purok')
            ->join('user_information', 'user_information.user_id = users.id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('users.email', $search)
                ->orLike('user_information.first_name', $search)
                ->orLike('user_information.last_name', $search)
                ->orLike('users.id', $search)
                ->groupEnd();
        }

        if ($purok) {
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
        foreach ($users as $user) {
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

    public function pendingAccounts()
    {
        $usersModel = new \App\Models\UsersModel();
        $users = $usersModel
            ->select('users.id, users.email, users.status, user_information.first_name, user_information.last_name, user_information.purok, user_information.barangay')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->where('users.status', 'pending')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();

        if ($this->request->getGet('ajax')) {
            return $this->response->setJSON($users);
        }

        return view('admin/pendingAccounts', ['users' => $users]);
    }

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
            'status'        => $user['status'] ?? '',
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

    public function gcashsettings() { return view('admin/gcash_settings'); }

    // Save GCash Settings (AJAX)
    public function saveGcashSettings()
    {
        try {
            if (!$this->request->is('post')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
            }

            $model = new \App\Models\GcashSettingsModel();

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

    // Show transaction records
    public function transactionRecords()
    {
        $data = [
            'current_month' => date('Y-m-d')
        ];
        return view('admin/transaction_records', $data);
    }

    // Show monthly payments with filters
    public function monthlyPayments()
    {
        $month = $this->request->getGet('month') ?? date('Y-m');
        $method = $this->request->getGet('method') ?? '';
        $search = $this->request->getGet('search') ?? '';

        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $filters = [
            'month' => $month,
            'method' => $method,
            'search' => $search
        ];

        try {
            $payments = $this->paymentsModel->getMonthlyPayments($filters);
            $stats = $this->paymentsModel->getMonthlyStats($filters);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching payments: ' . $e->getMessage());
            $payments = [];
            $stats = [];
        }

        $formattedPayments = array_map(function ($payment) {
            $methodValue = strtolower($payment['method'] ?? 'unknown');
            $validMethods = ['offline', 'manual', 'gateway'];

            if (!in_array($methodValue, $validMethods)) {
                $methodValue = 'unknown';
            }

            return [
                'id' => $payment['id'],
                'user_name' => $payment['user_name'] ?? 'Unknown',
                'email' => $payment['email'] ?? 'N/A',
                'amount' => $payment['amount'] ?? 0,
                'method' => $methodValue,
                'status' => strtolower($payment['status'] ?? 'pending'),
                'reference_number' => $payment['reference_number'] ?? '-',
                'admin_reference' => $payment['admin_reference'] ?? '',
                'receipt_image' => $payment['receipt_image'] ? base_url($payment['receipt_image']) : null,
                'created_at' => $payment['created_at'] ?? date('Y-m-d H:i:s'),
                'paid_at' => $payment['paid_at'] ?? null,
                'avatar' => $payment['avatar'] ?? base_url('assets/images/default-avatar.png')
            ];
        }, $payments);

        $data = [
            'title' => 'Monthly Payments',
            'payments' => $formattedPayments,
            'stats' => $stats,
            'current_month' => $month,
            'filters' => $filters
        ];

        return view('admin/monthly_payments', $data);
    }

    // Fetch payments data (AJAX)
    public function getPaymentsData()
    {
        $month = $this->request->getGet('month');
        $method = $this->request->getGet('method');
        $search = $this->request->getGet('search');
        $all = $this->request->getGet('all');

        $filters = [
            'month' => $month,
            'method' => $method,
            'search' => $search
        ];

        if (!$all) {
            $page = (int)$this->request->getGet('page') ?: 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
        }

        $payments = $this->paymentsModel->getMonthlyPayments($filters);
        $stats = $this->paymentsModel->getMonthlyStats($filters);

        $formattedPayments = array_map(function ($payment) {
            $methodValue = strtolower($payment['method'] ?? 'unknown');
            $validMethods = ['offline', 'manual', 'gateway'];

            if (!in_array($methodValue, $validMethods)) {
                $methodValue = 'unknown';
            }

            return [
                'id' => $payment['id'],
                'user_name' => $payment['user_name'] ?? 'Unknown',
                'email' => $payment['email'] ?? 'N/A',
                'amount' => $payment['amount'] ?? 0,
                'method' => $methodValue,
                'status' => strtolower($payment['status'] ?? 'pending'),
                'reference_number' => $payment['reference_number'] ?? '-',
                'admin_reference' => $payment['admin_reference'] ?? '',
                'receipt_image' => $payment['receipt_image'] ? base_url($payment['receipt_image']) : null,
                'created_at' => $payment['created_at'] ?? date('Y-m-d H:i:s'),
                'paid_at' => $payment['paid_at'] ?? null,
                'avatar' => $payment['avatar'] ?? base_url('assets/images/default-avatar.png')
            ];
        }, $payments);

        return $this->response->setJSON([
            'success' => true,
            'payments' => $formattedPayments,
            'stats' => $stats
        ]);
    }

    // Confirm GCash Payment (AJAX)
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

    // Get users by Purok (AJAX)
    public function getUsersByPurok($purok)
    {
        $userModel = new \App\Models\UserInformationModel();
        $purok = (int) $purok;

        $users = $userModel->select('user_id, first_name, last_name')
            ->where('purok', $purok)
            ->orderBy('first_name', 'ASC')
            ->findAll();

        return $this->response->setJSON($users);
    }

    // Get pending billings for a user (AJAX)
    public function getPendingBillings($userId)
    {
        $month = $this->request->getGet('month');
        $billingModel = new \App\Models\BillingModel();

        $billings = $billingModel->getPendingBillingsByUserAndMonth($userId, $month);

        return $this->response->setJSON($billings);
    }

    // Add counter payment (AJAX)
    public function addCounterPayment()
    {
        try {
            $data = $this->request->getJSON(true);

            if (!$data || empty($data['user_id']) || empty($data['billing_id']) || empty($data['amount'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Required fields missing']);
            }

            $billingModel = new \App\Models\BillingModel();
            $paymentsModel = new \App\Models\PaymentsModel();

            $billing = $billingModel->find($data['billing_id']);
            if (!$billing) {
                return $this->response->setJSON(['success' => false, 'message' => 'Billing not found']);
            }

            if ($billing['user_id'] != $data['user_id']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Billing does not match user']);
            }

            if (floatval($data['amount']) != floatval($billing['amount_due'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Amount does not match billing']);
            }

            $adminRef = $data['admin_reference'] ?? ('CNT-' . date('YmdHis') . '-' . rand(100, 999));

            $paymentsModel->insert([
                'user_id' => $data['user_id'],
                'billing_id' => $data['billing_id'],
                'amount' => $data['amount'],
                'method' => 'offline',
                'status' => 'Paid',
                'payment_intent_id' => null,
                'payment_method_id' => null,
                'reference_number' => null,
                'admin_reference' => $adminRef,
                'paid_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $billingModel->update($data['billing_id'], ['status' => 'Paid', 'paid_date' => date('Y-m-d H:i:s')]);

            return $this->response->setJSON(['success' => true]);

        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // Export payments data (CSV)
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

        $payments = $this->paymentsModel->getMonthlyPayments($filters);

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

    // ---------------- Billing Functions ----------------

    /**
     * Display billing management page
     */
    public function billingManagement()
    {
        return view('admin/billing_management');
    }

    /**
     * Get all billings (AJAX endpoint for billing management)
     */
    public function getAllBillings()
    {
        try {
            $month = $this->request->getGet('month') ?? '';
            $status = $this->request->getGet('status') ?? '';
            $search = $this->request->getGet('search') ?? '';
            
            $builder = $this->billingModel
                ->select('billings.*, 
                         CONCAT(user_information.first_name, " ", user_information.last_name) as user_name,
                         users.email')
                ->join('users', 'users.id = billings.user_id', 'left')
                ->join('user_information', 'user_information.user_id = users.id', 'left');

            // Apply filters
            if (!empty($month)) {
                $builder->where('DATE_FORMAT(billings.due_date, "%Y-%m")', $month);
            }

            if (!empty($status)) {
                $builder->where('billings.status', $status);
            }

            if (!empty($search)) {
                $builder->groupStart()
                    ->like('user_information.first_name', $search)
                    ->orLike('user_information.last_name', $search)
                    ->orLike('users.email', $search)
                    ->orLike('billings.bill_no', $search)
                    ->groupEnd();
            }

            $billings = $builder->orderBy('billings.created_at', 'DESC')->findAll();

            return $this->response->setJSON([
                'success' => true,
                'billings' => $billings
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getAllBillings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load billings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Synchronize billings (for automatic billing setup)
     */
    public function synchronizeBillings()
    {
        try {
            $input = $this->request->getJSON(true);
            $month = $input['month'] ?? date('Y-m');
            
            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid month format'
                ]);
            }
            
            // Get all active users with their complete information
            $users = $this->usersModel
                ->select('users.id, 
                         user_information.family_number,
                         user_information.age,
                         user_information.first_name,
                         user_information.last_name')  // ✅ Removed household_type
                ->join('user_information', 'user_information.user_id = users.id', 'inner')
                ->where('users.status', 'approved')
                ->where('users.active', 2)
                ->where('user_information.family_number IS NOT NULL')  // Must have family number
                ->where('user_information.family_number !=', '')       // Family number not empty
                ->where('user_information.age IS NOT NULL')            // Must have age
                ->where('user_information.age >', 0)                   // Age must be valid
                ->findAll();
            
            if (empty($users)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No eligible users found with complete profile information (family number and age required)'
                ]);
            }
            
            $created = 0;
            $existing = 0;
            $skippedIncomplete = 0;
            
            foreach ($users as $user) {
                // Determine billing rate based on family composition and age
                $amount = $this->calculateBillingAmount($user);
                
                if ($amount === null) {
                    $skippedIncomplete++;
                    continue;
                }
                
                // Check if billing already exists for this user and month
                $existingBilling = $this->billingModel
                    ->where('user_id', $user['id'])
                    ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
                    ->first();
                
                if ($existingBilling) {
                    $existing++;
                    continue;
                }
                
                // Create new billing record
                $billingData = [
                    'user_id' => $user['id'],
                    'bill_no' => 'BILL-' . date('Ymd') . '-' . str_pad($user['id'], 4, '0', STR_PAD_LEFT),
                    'amount_due' => $amount,
                    'billing_month' => $month . '-01',
                    'due_date' => date('Y-m-t', strtotime($month . '-01')), // Last day of month
                    'status' => 'Pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($this->billingModel->insert($billingData)) {
                    $created++;
                }
            }
            
            $message = "Synchronization completed! Created: {$created} new billings";
            if ($existing > 0) {
                $message .= ", Skipped: {$existing} existing billings";
            }
            if ($skippedIncomplete > 0) {
                $message .= ", Skipped: {$skippedIncomplete} users with incomplete data";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'stats' => [
                    'created' => $created,
                    'existing' => $existing,
                    'skipped_incomplete' => $skippedIncomplete,
                    'total_users' => count($users)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in synchronizeBillings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to synchronize billings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate billing amount based on user's family composition and age
     * @param array $user User data with family_number and age
     * @return float|null Billing amount or null if data is incomplete
     */
    private function calculateBillingAmount($user)
    {
        $familyNumber = (int) ($user['family_number'] ?? 0);
        $age = (int) ($user['age'] ?? 0);
        
        // Validate required data
        if ($familyNumber <= 0 || $age <= 0) {
            return null; // Skip users with invalid data
        }
        
        // Senior Citizen (60+ years old) - ₱48
        if ($age >= 60) {
            return 48.00;
        }
        
        // Family (2+ family members) - ₱60  
        if ($familyNumber >= 2) {
            return 60.00;
        }
        
        // Solo (1 family member, under 60) - ₱30
        return 30.00;
    }

    public function reports()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Calculate rate-based household counts
        $normalCount = $this->userInfoModel
            ->where('(age < 60 OR age IS NULL)')
            ->where('family_number !=', 1)
            ->countAllResults();
        
        $seniorCount = $this->userInfoModel
            ->where('age >=', 60)
            ->where('family_number !=', 1)
            ->countAllResults();
        
        $aloneCount = $this->userInfoModel
            ->where('family_number', 1)
            ->countAllResults();
        
        $totalHouseholds = $normalCount + $seniorCount + $aloneCount;
        
        // Fixed rates
        $rateNormal = 60;
        $rateSenior = 48;
        $rateAlone = 30;
        
        // Expected monthly collection
        $monthlyExpected = ($normalCount * $rateNormal) + ($seniorCount * $rateSenior) + ($aloneCount * $rateAlone);
        
        // Current month collection
        $currentMonthCollected = $this->billingModel
            ->where('status', 'Paid')
            ->where('MONTH(updated_at)', $currentMonth)
            ->where('YEAR(updated_at)', $currentYear)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0;
        
        // Paid households this month (distinct users), based on updated_at
        $paidHouseholds = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('status', 'Paid')
            ->where('MONTH(updated_at)', $currentMonth)
            ->where('YEAR(updated_at)', $currentYear)
            ->get()
            ->getRow()->c ?? 0);
        
        // Pending amount and count
        $pendingAmount = (float)($this->billingModel
            ->where('status', 'Pending')
            ->where('MONTH(updated_at)', $currentMonth)
            ->where('YEAR(updated_at)', $currentYear)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0);
        
        $pendingCount = (int)($this->billingModel
            ->where('status', 'Pending')
            ->where('MONTH(updated_at)', $currentMonth)
            ->where('YEAR(updated_at)', $currentYear)
            ->countAllResults());
        
        // Late payments (overdue)
        $latePayments = $this->billingModel
            ->where('status', 'Pending')
            ->where('due_date <', date('Y-m-d'))
            ->countAllResults();
        
        // If no data for current month, fallback to Year-To-Date for status overview
        $statusScope = 'MONTH';
        if ((int)$paidHouseholds + (int)$pendingCount + (int)$latePayments === 0) {
            // Recompute paid and pending within current year using updated_at
            $paidHouseholds = (int)($this->billingModel
                ->select('COUNT(DISTINCT user_id) as c')
                ->where('status', 'Paid')
                ->where('YEAR(updated_at)', $currentYear)
                ->get()
                ->getRow()->c ?? 0);

            $pendingCount = (int)($this->billingModel
                ->where('status', 'Pending')
                ->where('YEAR(updated_at)', $currentYear)
                ->countAllResults());

            // Late payments within current year and overdue (by due_date year)
            $latePayments = (int)($this->billingModel
                ->where('status', 'Pending')
                ->where('YEAR(due_date)', $currentYear)
                ->where('due_date <', date('Y-m-d'))
                ->countAllResults());

            if ((int)$paidHouseholds + (int)$pendingCount + (int)$latePayments > 0) {
                $statusScope = 'YTD';
            }
        }

        // Collection rate
        $collectionRate = $totalHouseholds > 0 ? round(($paidHouseholds / $totalHouseholds) * 100, 1) : 0;
        
        // Monthly collection rates and amounts for chart
        $monthlyData = $this->billingModel
            ->select("MONTH(updated_at) as month_num, 
                     COUNT(DISTINCT user_id) as paid_count,
                     SUM(amount_due) as total_amount")
            ->where('status', 'Paid')
            ->where('YEAR(updated_at)', $currentYear)
            ->groupBy('MONTH(updated_at)')
            ->orderBy('MONTH(updated_at)', 'ASC')
            ->get()
            ->getResultArray();
        
        $collectionRates = array_fill(0, 12, 0);
        $collectionAmounts = array_fill(0, 12, 0);
        
        foreach ($monthlyData as $data) {
            $monthIndex = (int)$data['month_num'] - 1;
            $paidCount = (int)$data['paid_count'];
            $collectionRates[$monthIndex] = $totalHouseholds > 0 ? round(($paidCount / $totalHouseholds) * 100, 1) : 0;
            $collectionAmounts[$monthIndex] = (float)$data['total_amount'];
        }

        // If no data for current year, fallback to last 12 months rolling window
        if (array_sum($collectionAmounts) == 0) {
            $twelveMonthsAgo = date('Y-m-01', strtotime('-11 months'));
            $monthlyData = $this->billingModel
                ->select("DATE_FORMAT(updated_at, '%Y-%m') as ym, MONTH(updated_at) as month_num, COUNT(DISTINCT user_id) as paid_count, SUM(amount_due) as total_amount")
                ->where('status', 'Paid')
                ->where('DATE(updated_at) >=', $twelveMonthsAgo)
                ->groupBy('YEAR(updated_at), MONTH(updated_at)')
                ->orderBy('YEAR(updated_at)', 'ASC')
                ->orderBy('MONTH(updated_at)', 'ASC')
                ->get()
                ->getResultArray();

            $collectionRates = array_fill(0, 12, 0);
            $collectionAmounts = array_fill(0, 12, 0);
            foreach ($monthlyData as $row) {
                $idx = ((int)$row['month_num'] - 1) % 12;
                $pc = (int)$row['paid_count'];
                $collectionRates[$idx] = $totalHouseholds > 0 ? round(($pc / $totalHouseholds) * 100, 1) : 0;
                $collectionAmounts[$idx] = (float)$row['total_amount'];
            }
        }
        
        $viewData = [
            'normalCount' => $normalCount,
            'seniorCount' => $seniorCount,
            'aloneCount' => $aloneCount,
            'totalHouseholds' => $totalHouseholds,
            'rateNormal' => $rateNormal,
            'rateSenior' => $rateSenior,
            'rateAlone' => $rateAlone,
            'monthlyExpected' => $monthlyExpected,
            'currentMonthCollected' => $currentMonthCollected,
            'paidHouseholds' => $paidHouseholds,
            'pendingAmount' => $pendingAmount,
            'pendingCount' => $pendingCount,
            'latePayments' => $latePayments,
            'collectionRate' => $collectionRate,
            'collectionRates' => $collectionRates,
            'collectionAmounts' => $collectionAmounts,
            'statusScope' => $statusScope
        ];
        
        if ($this->request->isAJAX()) {
            return view('admin/reports-content', $viewData);
        }
        return view('admin/reports', $viewData);
    }


    // ---------------- Account Functions ----------------

    // Edit user/admin profile
    public function editProfile()
    {
        return view('admin/edit_profile');
    }


    public function activateUser($id)
    {
        $this->usersModel->update($id, ['active' => 2, 'status' => 'approved']);
        return redirect()->back()->with('success', 'User activated successfully.');
    }

    public function deactivateUser($id)
    {
        $this->usersModel->update($id, ['active' => 1, 'status' => 'inactive']);
        return redirect()->back()->with('success', 'User deactivated successfully.');
    }

    public function suspendUser($id)
    {
        $this->usersModel->update($id, [
            'active' => -1,
            'status' => 'suspended'
        ]);
        return redirect()->back()->with('success', 'User suspended successfully.');
    }

    public function approve($id)
    {
        if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

        if ($this->usersModel->update($id, ['status' => 'approved', 'active' => 2])) {
            return redirect()->back()->with('success', 'User approved successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve user.');
    }

    public function reject($id)
    {
        if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

        if ($this->usersModel->update($id, ['status' => 'rejected'])) {
            return redirect()->back()->with('success', 'User rejected successfully.');
        }

        return redirect()->back()->with('error', 'Failed to reject user.');
    }

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

    public function toggleUserStatus($id)
    {
        $user = $this->usersModel->find($id);
        if (!$user) return redirect()->back()->with('error', 'User not found.');

        $newStatus = $user['active'] ? 0 : 1;
        $this->usersModel->update($id, ['active' => $newStatus]);

        return redirect()->back()->with('success', 'User status updated.');
    }

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

    public function announcements()
    {
        return view('admin/announcements', ['title' => 'Announcements']);
    }

    public function paymentSettings()
    {
        $data = [
            'title' => 'Payment Settings',
            'payment' => $this->paymentModel->first(),
        ];

        return view('admin/paymentSettings', $data);
    }

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

    public function profile()
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) return redirect()->to(base_url('admin/login'))->with('error', 'You must be logged in.');

        $admin = $this->adminModel->find($adminId);
        if (!$admin) return redirect()->to(base_url('admin/login'))->with('error', 'Admin not found.');

        return view('admin/profile', ['admin' => $admin]);
    }

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