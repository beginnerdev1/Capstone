<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\GcashSettingsModel;
use App\Models\PaymentsModel;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

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
                    ->whereIn('status', ['Paid', 'Over the Counter'])
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
                    ->whereIn('status', ['Paid', 'Over the Counter'])
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
            ->where('users.status', 'Pending')
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

    // GCash Settings Page
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


    // =======================Transaction Payments===============================
    // Show transaction records
    public function transactionRecords()
    {
        $data = [
            'current_month' => date('Y-m-d')
        ];
        return view('admin/transaction_records', $data);
    }

    // Show monthly payments with filters - Transaction Records
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

    // Fetch payments data (AJAX) - Transaction Payments
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

    // Confirm GCash Payment (AJAX) - Transaction Payments
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

    // Get users by Purok (AJAX) - Transaction Payments
    public function getUsersByPurok($purok)
    {
        $userModel = new \App\Models\UserInformationModel();
        $purok = (int) $purok;
        
        // Check if we should exclude users with existing billings
        $excludeBilled = $this->request->getGet('exclude_billed');
        $month = $this->request->getGet('month');
        
        // Debug logging
        log_message('info', "getUsersByPurok Debug - Purok: {$purok}, Exclude Billed: {$excludeBilled}, Month: {$month}");
        
        $builder = $userModel->select('user_information.user_id, user_information.first_name, user_information.last_name')
            ->join('users', 'users.id = user_information.user_id', 'inner')
            ->where('user_information.purok', $purok)
            ->whereIn('users.status', ['approved', 'Approved']) // Handle both status values
            ->orderBy('user_information.first_name', 'ASC');
        
        // If exclude_billed is set and month is provided, filter out users with existing billings
        if ($excludeBilled && $month) {
            // Ensure month is in YYYY-MM format
            if (preg_match('/^\d{4}-\d{2}$/', $month)) {
                $builder->whereNotExists(function($subquery) use ($month) {
                    $subquery->select('1')
                        ->from('billings')
                        ->where('billings.user_id = user_information.user_id')
                        ->where('DATE_FORMAT(billings.billing_month, "%Y-%m") = "' . $month . '"');
                });
            }
        }
        
        $users = $builder->findAll();
        
        // More detailed debug logging
        log_message('info', "getUsersByPurok Result - Found " . count($users) . " users");
        foreach ($users as $user) {
            log_message('info', "User: ID {$user['user_id']}, Name: {$user['first_name']} {$user['last_name']}");
        }
        
        return $this->response->setJSON($users);
    }

    // Get pending billings for a user (AJAX) - transaction Payments
    public function getPendingBillings($userId)
    {
        $month = $this->request->getGet('month');
        $billingModel = new \App\Models\BillingModel();

        $billings = $billingModel->getPendingBillingsByUserAndMonth($userId, $month);

        return $this->response->setJSON($billings);
    }

    // Add counter payment (AJAX) - Transaction Payments
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

    // Export payments data (CSV) - Transaction Payments
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


     //Display billing management page
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
            $purok = $this->request->getGet('purok') ?? '';  // Add this line
            $status = $this->request->getGet('status') ?? '';
            $search = $this->request->getGet('search') ?? '';
            
            $builder = $this->billingModel
                ->select('billings.*, 
                         CONCAT(user_information.first_name, " ", user_information.last_name) as user_name,
                         users.email, user_information.purok')  // Add purok to select
                ->join('users', 'users.id = billings.user_id', 'left')
                ->join('user_information', 'user_information.user_id = users.id', 'left');

            // Apply filters
            if (!empty($month)) {
                $builder->where('DATE_FORMAT(billings.billing_month, "%Y-%m")', $month);  // Use billing_month instead
            }

            if (!empty($purok)) {  // Add purok filter
                $builder->where('user_information.purok', $purok);
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

    //Synchronize billings (for automatic billing setup)
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
            
            // ✅ Check if billing for this month has already been synchronized
            $existingBillingsForMonth = $this->billingModel
                ->where('DATE_FORMAT(billing_month, "%Y-%m")', date('Y-m')) // Check current month instead
                ->countAllResults();

            if ($existingBillingsForMonth > 0) {
                $monthName = date('F Y');
                $approvedUsersCount = $this->usersModel
                    ->whereIn('status', ['approved', 'Approved'])
                    ->countAllResults();
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "⚠️ Billing synchronization for {$monthName} has already been completed! Found {$existingBillingsForMonth} existing billings out of {$approvedUsersCount} approved users. You cannot synchronize for the same month twice."
                ]);
            }
            
            // ✅ FIXED - Focus on approved users only, regardless of other fields
            $users = $this->usersModel
                ->select('users.id, 
                         users.status,
                         users.active,
                         users.email,
                         user_information.family_number,
                         user_information.age,
                         user_information.first_name,
                         user_information.last_name')
                ->join('user_information', 'user_information.user_id = users.id', 'left')
                ->whereIn('users.status', ['approved', 'Approved'])  // Only approved users
                ->findAll();
            
            // Debug information about what users we found
            log_message('info', 'Billing Sync: Found ' . count($users) . ' users with approved status');
            
            if (empty($users)) {
                // Let's check what users actually exist in the database
                $allUsers = $this->usersModel
                    ->select('users.id, users.email, users.status, users.active, users.is_verified, users.profile_complete')
                    ->limit(10)
                    ->findAll();
                
                $debugInfo = '';
                if (!empty($allUsers)) {
                    $debugInfo = ' Debug - Found ' . count($allUsers) . ' total users. Sample statuses: ';
                    foreach (array_slice($allUsers, 0, 5) as $u) {
                        $debugInfo .= "[ID:{$u['id']} Status:'{$u['status']}' Active:{$u['active']}] ";
                    }
                } else {
                    $debugInfo = ' No users found in database at all.';
                }
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No approved users found for billing synchronization.' . $debugInfo
                ]);
            }
            
            $created = 0;
            $existing = 0;
            $processed = 0;
            $skipped = 0;
            $emailsSent = 0; // ✅ ADD EMAIL COUNTER
            
            foreach ($users as $user) {
                $processed++;
                
                // Use default values for missing family/age data
                $familyNumber = (int) ($user['family_number'] ?? 1);
                $age = (int) ($user['age'] ?? 30);
                
                // Ensure minimum valid values
                if ($familyNumber <= 0) $familyNumber = 1;
                if ($age <= 0) $age = 30;
                
                // Prepare user data with defaults for billing calculation
                $userForCalculation = [
                    'family_number' => $familyNumber,
                    'age' => $age
                ];
                
                // Calculate billing amount
                $amount = $this->calculateBillingAmount($userForCalculation);
                
                // Check if billing already exists for this user and month (using due_date)
                $existingBilling = $this->billingModel
                    ->where('user_id', $user['id'])
                    ->where('DATE_FORMAT(billing_month, "%Y-%m")', date('Y-m')) // Check current month
                    ->first();
                
                if ($existingBilling) {
                    $existing++;
                    $skipped++;
                    log_message('info', "Billing sync: User {$user['id']} already has billing for current month");
                    continue;
                }
                
                // Create new billing record with current date and due date 1 week later
                $syncDate = date('Y-m-d');
                $dueDate = date('Y-m-d', strtotime('+7 days'));
                $billNo = 'BILL-' . date('Ymd') . '-' . str_pad($user['id'], 4, '0', STR_PAD_LEFT);
                
                $billingData = [
                    'user_id' => $user['id'],
                    'bill_no' => $billNo,
                    'amount_due' => $amount,
                    'billing_month' => $syncDate, // Date when admin synchronized
                    'due_date' => $dueDate, // Due date is 7 days from synchronization
                    'status' => 'Pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($this->billingModel->insert($billingData)) {
                    $created++;
                    log_message('info', "Billing sync: Created billing for User {$user['id']} - Amount: ₱{$amount} - Sync Date: {$syncDate} - Due Date: {$dueDate}");
                    
                    // ✅ SEND EMAIL NOTIFICATION
                    if (!empty($user['email'])) {
                        $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                        if (empty($userName)) {
                            $userName = 'Valued Customer';
                        }
                        
                        $this->sendBillingNotificationEmail(
                            $user['email'],
                            $userName,
                            $billNo,
                            $amount,
                            $dueDate,
                            $syncDate
                        );
                        $emailsSent++;
                    } else {
                        log_message('warning', "No email address found for User {$user['id']}, skipping email notification");
                    }
                    
                } else {
                    $skipped++;
                    log_message('error', "Billing sync: Failed to create billing for User {$user['id']}");
                }
            }
            
            // Build success message with detailed stats
            $monthName = date('F Y', strtotime($month . '-01'));
            $message = "✅ Billing synchronization for {$monthName} completed successfully! ";
            $message .= "Processed: {$processed} approved users, ";
            $message .= "Created: {$created} new billings, ";
            $message .= "Emails sent: {$emailsSent}"; // ✅ ADD EMAIL COUNT
            
            if ($existing > 0) {
                $message .= ", Skipped: {$existing} existing billings";
            }
            
            if ($skipped > $existing) {
                $failedCount = $skipped - $existing;
                $message .= ", Failed: {$failedCount} billing creations";
            }
            
            // Log the results
            log_message('info', "Billing synchronization completed - {$message}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'stats' => [
                    'month' => $monthName,
                    'processed' => $processed,
                    'created' => $created,
                    'existing' => $existing,
                    'skipped' => $skipped,
                    'emails_sent' => $emailsSent, // ✅ ADD EMAIL STATS
                    'total_approved_users' => count($users)
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
     * @return float Billing amount (always returns valid amount)
     */
    private function calculateBillingAmount($user)
    {
        $familyNumber = (int) ($user['family_number'] ?? 1);
        $age = (int) ($user['age'] ?? 30);
        
        // Ensure valid defaults
        if ($familyNumber <= 0) $familyNumber = 1;
        if ($age <= 0) $age = 30;
        
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

  // ✅ ADD THIS NEW METHOD FOR BILLING NOTIFICATION EMAILS
    /**
     * Send billing notification email using Brevo
     * @param string $toEmail User's email address
     * @param string $userName User's full name
     * @param string $billNo Bill number
     * @param float $amount Billing amount
     * @param string $dueDate Due date
     * @param string $billingMonth Billing month
     */
    private function sendBillingNotificationEmail($toEmail, $userName, $billNo, $amount, $dueDate, $billingMonth)
    {
        try {
            require ROOTPATH . 'vendor/autoload.php';

            $config = Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', getenv('BREVO_API_KEY'));

            $apiInstance = new TransactionalEmailsApi(
                new Client(),
                $config
            );

            // Format the billing month for display
            $monthDisplay = date('F Y', strtotime($billingMonth));
            $dueDateDisplay = date('F d, Y', strtotime($dueDate));
            $amountDisplay = number_format($amount, 2);

            $email = new SendSmtpEmail([
                'subject' => "Water Billing Statement - {$monthDisplay}",
                'sender' => ['name' => 'Water Billing System', 'email' => getenv('SMTP_FROM')],
                'to' => [['email' => $toEmail]],
                'htmlContent' => "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                        <div style='background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                            <h2 style='margin: 0; text-align: center;'>💧 Water Billing Statement</h2>
                            <p style='margin: 10px 0 0 0; text-align: center; opacity: 0.9;'>Monthly Water Service Bill</p>
                        </div>
                        
                        <div style='padding: 20px; background: #f8fafc; border-radius: 8px; margin-bottom: 20px;'>
                            <p style='margin: 0 0 10px 0; font-size: 16px;'><strong>Dear {$userName},</strong></p>
                            <p style='margin: 0; color: #4b5563; line-height: 1.6;'>Your water billing statement for {$monthDisplay} has been generated. Please review the details below and ensure payment is made before the due date.</p>
                        </div>
                        
                        <div style='background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 20px;'>
                            <h3 style='margin: 0 0 15px 0; color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 8px;'>📋 Billing Details</h3>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; font-weight: 600; color: #374151;'>Bill Number:</td>
                                    <td style='padding: 8px 0; color: #6b7280;'>{$billNo}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; font-weight: 600; color: #374151;'>Billing Period:</td>
                                    <td style='padding: 8px 0; color: #6b7280;'>{$monthDisplay}</td>
                                </tr>
                                <tr style='background: #fef3c7;'>
                                    <td style='padding: 12px 8px; font-weight: 700; color: #92400e; font-size: 16px;'>Amount Due:</td>
                                    <td style='padding: 12px 8px; font-weight: 700; color: #92400e; font-size: 18px;'>₱{$amountDisplay}</td>
                                </tr>
                                <tr style='background: #fee2e2;'>
                                    <td style='padding: 12px 8px; font-weight: 700; color: #dc2626; font-size: 16px;'>Due Date:</td>
                                    <td style='padding: 12px 8px; font-weight: 700; color: #dc2626; font-size: 16px;'>{$dueDateDisplay}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div style='background: #ecfdf5; padding: 16px; border-radius: 8px; border-left: 4px solid #10b981; margin-bottom: 20px;'>
                            <h4 style='margin: 0 0 8px 0; color: #065f46;'>💡 Payment Reminder</h4>
                            <p style='margin: 0; color: #047857; font-size: 14px;'>Please ensure your payment is completed before the due date to avoid any service interruption. You can pay through our online portal or visit our office during business hours.</p>
                        </div>
                        
                        <div style='text-align: center; padding: 20px; background: #f1f5f9; border-radius: 8px;'>
                            <p style='margin: 0 0 10px 0; color: #475569; font-size: 14px;'>Thank you for using our Water Billing System.</p>
                            <p style='margin: 0; color: #94a3b8; font-size: 12px;'>This is an automated message. Please do not reply to this email.</p>
                        </div>
                    </div>
                ",
            ]);

            $apiInstance->sendTransacEmail($email);
            log_message('info', "Billing notification sent to {$toEmail} for bill {$billNo}");
            
        } catch (\Exception $e) {
            log_message('error', "Failed to send billing notification to {$toEmail}: " . $e->getMessage());
        }
    }


    //Get billing statistics for the current month
    public function getBillingStatistics()
    {
        try {
            $month = $this->request->getGet('month') ?? date('Y-m');
            
            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid month format'
                ]);
            }
            
            // Get billing statistics by amount (which corresponds to billing type)
            $stats = $this->billingModel
                ->select("
                    SUM(CASE WHEN amount_due = 30.00 THEN 1 ELSE 0 END) as solo_count,
                    SUM(CASE WHEN amount_due = 48.00 THEN 1 ELSE 0 END) as senior_count,
                    SUM(CASE WHEN amount_due = 60.00 THEN 1 ELSE 0 END) as family_count,
                    COUNT(*) as total_count
                ")
                ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
                ->get()
                ->getRow();
                
            if (!$stats) {
                $stats = (object)[
                    'solo_count' => 0,
                    'senior_count' => 0,
                    'family_count' => 0,
                    'total_count' => 0
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'statistics' => [
                    'solo' => (int)$stats->solo_count,
                    'senior' => (int)$stats->senior_count,
                    'family' => (int)$stats->family_count,
                    'total' => (int)$stats->total_count
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getBillingStatistics: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ]);
        }
    }

    // Get users by Purok for Manual Billing (excludes users with existing billings)
    public function getUsersByPurokForManualBilling($purok)
    {
        try {
            $userModel = new \App\Models\UserInformationModel();
            $purok = (int) $purok;
            
            $month = $this->request->getGet('month');
            
            // ✅ Include family_number and age for automatic amount calculation
            $builder = $userModel->select('user_information.user_id, user_information.first_name, user_information.last_name, user_information.family_number, user_information.age')
                ->join('users', 'users.id = user_information.user_id', 'inner')
                ->where('user_information.purok', $purok)
                ->whereIn('users.status', ['approved', 'Approved']) // Handle both status values
                ->orderBy('user_information.first_name', 'ASC');
        
            // Always filter out users with existing billings for the selected month
            if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
                // Get users with billings first, then exclude them
                $usersWithBillings = $this->billingModel
                    ->builder()  // ✅ Get the Query Builder instance
                    ->distinct()
                    ->select('user_id')
                    ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
                    ->get()
                    ->getResultArray();
            
                $userIdsWithBillings = array_column($usersWithBillings, 'user_id');
            
                if (!empty($userIdsWithBillings)) {
                    $builder->whereNotIn('user_information.user_id', $userIdsWithBillings);
                }
            }
            
            $users = $builder->findAll();
            
            // ✅ Calculate billing amount for each user
            foreach ($users as &$user) {
                $user['calculated_amount'] = $this->calculateBillingAmount($user);
            }
            
            return $this->response->setJSON($users);
            
        } catch (\Exception $e) {
            log_message('error', "getUsersByPurokForManualBilling Error: " . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to load users',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Create Manual Billing (AJAX)
  public function createManualBilling()
    {
        try {
            $input = $this->request->getJSON(true);
            
            if (!$input || empty($input['user_id']) || empty($input['month']) || empty($input['amount'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields'
                ]);
            }
            
            $userId = (int)$input['user_id'];
            $month = $input['month'];
            $amount = (float)$input['amount'];
            $purok = $input['purok'] ?? '';
            
            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid month format'
                ]);
            }
            
            // Check if user exists and get user details for email
            $user = $this->usersModel
                ->select('users.id, users.email, user_information.first_name, user_information.last_name')
                ->join('user_information', 'user_information.user_id = users.id', 'left')
                ->where('users.id', $userId)
                ->first();
            
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }
            
            // Check if billing already exists for this user and month
            $existingBilling = $this->billingModel
                ->where('user_id', $userId)
                ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
                ->first();
            
            if ($existingBilling) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User already has a billing for this month'
                ]);
            }
            
            // Create billing record
            $today = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime('+7 days'));
            $billNo = 'MANUAL-' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT);
            
            $billingData = [
                'user_id' => $userId,
                'bill_no' => $billNo,
                'amount_due' => $amount,
                'billing_month' => $month . '-01', // First day of the month
                'due_date' => $dueDate,
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($this->billingModel->insert($billingData)) {
                $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                if (empty($userName)) {
                    $userName = 'Valued Customer';
                }
                
                // ✅ SEND EMAIL NOTIFICATION FOR MANUAL BILLING
                if (!empty($user['email'])) {
                    $this->sendBillingNotificationEmail(
                        $user['email'],
                        $userName,
                        $billNo,
                        $amount,
                        $dueDate,
                        $month . '-01'  // Use first day of month for display
                    );
                    
                    log_message('info', "Manual billing email sent to {$user['email']} for bill {$billNo}");
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "Manual billing created successfully for {$userName} - ₱{$amount}. Email notification sent!"
                    ]);
                } else {
                    log_message('warning', "Manual billing created for User {$userId} but no email address found, skipping email notification");
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "Manual billing created successfully for {$userName} - ₱{$amount}. (No email address found)"
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create manual billing'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error in createManualBilling: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create manual billing: ' . $e->getMessage()
            ]);
        }
    }







    // Billing Reports
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
            ->whereIn('status', ['Paid', 'Over the Counter'])
            ->where('MONTH(updated_at)', $currentMonth)
            ->where('YEAR(updated_at)', $currentYear)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0;
        
        // Paid households this month (distinct users), based on updated_at
        $paidHouseholds = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->whereIn('status', ['Paid', 'Over the Counter'])
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
                ->whereIn('status', ['Paid', 'Over the Counter'])
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
            ->whereIn('status', ['Paid', 'Over the Counter'])
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
                ->whereIn('status', ['Paid', 'Over the Counter'])
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