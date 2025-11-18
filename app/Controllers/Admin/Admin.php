<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\GcashSettingsModel;
use App\Models\PaymentsModel;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart as XlsChart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Legend;


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


    //------------------REGISTERED USERS-----------------------------
    
    // Registered Users page (view)
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

    // Add new user (AJAX)
    public function addUser()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            helper(['text']);

            $firstName     = trim((string)$this->request->getPost('first_name'));
            $lastName      = trim((string)$this->request->getPost('last_name'));
            $email         = trim((string)$this->request->getPost('email'));
            $password      = (string)$this->request->getPost('password');
            $phone         = trim((string)$this->request->getPost('phone'));
            $gender        = trim((string)$this->request->getPost('gender'));
            $age           = (int)$this->request->getPost('age');
            $familyNumber  = (int)$this->request->getPost('family_number');
            $purok         = trim((string)$this->request->getPost('purok'));
            $status        = strtolower(trim((string)$this->request->getPost('status') ?? 'approved'));

            $errors = [];
            if (strlen($firstName) < 2) $errors['first_name'] = 'First name must be at least 2 characters';
            if (strlen($lastName) < 2)  $errors['last_name']  = 'Last name must be at least 2 characters';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
            if (strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters';
            if ($purok === '' || !preg_match('/^\d+$/', $purok)) $errors['purok'] = 'Purok is required';
            if ($phone === '') $errors['phone'] = 'Contact number is required';
            if (!in_array($gender, ['Male','Female','Other'], true)) $errors['gender'] = 'Invalid gender';
            if ($age < 1 || $age > 120) $errors['age'] = 'Age must be between 1 and 120';
            if ($familyNumber < 1 || $familyNumber > 20) $errors['family_number'] = 'Family members must be between 1 and 20';

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $errors
                ]);
            }

            // Normalize email to lowercase for consistent storage and comparison
            $email = strtolower($email);

            // Ensure email is unique (case-insensitive)
            // Let CI escape the value so SQL is valid: generates LOWER(email) = 'value'
            $existing = $this->usersModel->where('LOWER(email)', $email)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email already exists',
                    'errors'  => ['email' => 'Email is already registered']
                ]);
            }

            // Normalize status and active flags
            $allowedStatuses = ['pending','approved','suspended','rejected','inactive'];
            if (!in_array($status, $allowedStatuses, true)) {
                $status = 'approved';
            }
            $active = ($status === 'approved') ? 2 : 1; // keep consistent with existing code
            $isVerified = ($status === 'approved') ? 1 : 0;

            // Start DB transaction
            $db = \Config\Database::connect();
            $db->transStart();

            // Create user
            $userId = $this->usersModel->insert([
                'email'            => $email,
                'password'         => password_hash($password, PASSWORD_DEFAULT),
                'status'           => $status,
                'active'           => $active,
                'is_verified'      => $isVerified,
                'profile_complete' => 1,
            ], true);

            if (!$userId) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create user account'
                ]);
            }

            // Create user information using model validation rules
            $infoResult = $this->userInfoModel->saveUserInfo($userId, [
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'phone'         => $phone,
                'gender'        => $gender,
                'age'           => $age,
                'family_number' => $familyNumber,
                'purok'         => (int)$purok,
                // Enforce fixed location for this deployment
                'barangay'      => 'Borlongan',
                'municipality'  => 'Dipaculao',
                'province'      => 'Aurora',
                'zipcode'       => '3203',
            ]);

            if (!$infoResult['success']) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $infoResult['message'] ?? 'Failed to save user info',
                    'errors'  => $infoResult['errors'] ?? []
                ]);
            }

            // Commit transaction
            $db->transComplete();
            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Database transaction failed'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User added successfully.',
                'id' => (int)$userId
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'addUser error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    // Inactive Users page (view)
    public function inactiveUsers()
    {
        return view('admin/inactive_users');
    }

    // Fetch inactive users (AJAX)
    public function getInactiveUsers()
    {
        $search = trim((string)$this->request->getGet('search'));
        $purok  = trim((string)$this->request->getGet('purok'));
        $from   = trim((string)$this->request->getGet('from'));
        $to     = trim((string)$this->request->getGet('to'));

        $db = \Config\Database::connect();
        $builder = $db->table('inactive_users iu')
            ->select('iu.id as inactive_id, iu.user_id, iu.email, iu.first_name, iu.last_name, iu.phone, iu.purok, iu.barangay, iu.municipality, iu.province, iu.zipcode, iu.inactivated_at, u.status, u.active')
            ->join('users u', 'u.id = iu.user_id', 'left')
            ->where('u.status', 'inactive')
            ->where('u.active', 1);

        if ($search !== '') {
            $builder->groupStart()
                ->like('iu.email', $search)
                ->orLike('iu.first_name', $search)
                ->orLike('iu.last_name', $search)
                ->groupEnd();
        }
        if ($purok !== '') {
            $builder->where('iu.purok', (int)$purok);
        }
        if ($from !== '' && $to !== '') {
            $builder->where('DATE(iu.inactivated_at) >=', $from)
                    ->where('DATE(iu.inactivated_at) <=', $to);
        }

        $rows = $builder->orderBy('iu.inactivated_at', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($rows);
    }

    // List archived bills (AJAX)
    public function getArchivedBills($inactiveId)
    {
        $db = \Config\Database::connect();
        $bills = $db->table('archived_billings')
            ->where('inactive_ref_id', (int)$inactiveId)
            ->orderBy('billing_month', 'DESC')
            ->get()->getResultArray();

        // Minimal formatting for client rendering
        $out = array_map(function($b){
            return [
                'id' => (int)$b['id'],
                'bill_no' => $b['bill_no'],
                'amount_due' => (float)($b['amount_due'] ?? 0),
                'billing_month' => $b['billing_month'],
                'due_date' => $b['due_date'],
                'status' => $b['status'],
                'paid_date' => $b['paid_date'],
                'archived_at' => $b['archived_at'],
            ];
        }, $bills);

        return $this->response->setJSON($out);
    }

    // Reactivate user (AJAX)
    public function reactivateUser($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $user = $this->usersModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Set to approved + active=2
        $this->usersModel->update($id, [
            'status' => 'approved',
            'active' => 2,
        ]);

        // Clean up any archived inactive snapshot for this user
        try {
            $db = \Config\Database::connect();
            $db->table('inactive_users')->where('user_id', $id)->delete();
        } catch (\Throwable $_) {
            // ignore cleanup errors
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to reactivate user']);
        }
        try {
            $logId = session()->get('admin_activity_log_id') ?? session()->get('superadmin_activity_log_id');
            if ($logId) {
                $info = $this->userInfoModel->getByUserId($id) ?? [];
                $user = $this->usersModel->find($id);
                $display = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? '')) ?: ($user['email'] ?? null);
                $details = [
                    'id' => $id,
                    'user_name' => $display,
                    'first_name' => $info['first_name'] ?? null,
                    'last_name' => $info['last_name'] ?? null,
                    'email' => $user['email'] ?? null,
                ];
                session()->set('skip_activity_logger', true);
                $logModel = new \App\Models\AdminActivityLogModel();
                $logModel->appendAction($logId, 'activate', '/admin/reactivateUser/' . $id, $this->request->getMethod() ?: 'POST', $id, $details);
            }
        } catch (\Throwable $_) {
            // ignore
        }

        return $this->response->setJSON(['success' => true, 'message' => 'User reactivated successfully']);
    }

    public function filterUsers()
    {
        $search = $this->request->getVar('search');
        $purok = $this->request->getVar('purok');
        $status = $this->request->getVar('status');
        // Build base query including pending bill counts per user (aggregate join)
        $builder = $this->usersModel
            ->select('users.id, users.email, users.status, user_information.first_name, user_information.last_name, user_information.purok, SUM(CASE WHEN billings.status = "Pending" THEN 1 ELSE 0 END) as pending_bills')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->join('billings', 'billings.user_id = users.id', 'left')
            ->groupBy('users.id');

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

        if ($status) {
            $builder->where('users.status', strtolower($status));
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
                'status' => $statusMap[$user['status']] ?? 'Unknown',
                'pending_bills' => (int)($user['pending_bills'] ?? 0),
            ];
        }

        return $this->response->setJSON($result);
    }


    //------------------VERIFY USER-----------------------------

    // Pending Accounts page (view)
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

    // Get user details by ID (AJAX)
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

    // Approve user account
public function approve($id)
{
    if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

    $user = $this->usersModel->find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    if ($this->usersModel->update($id, ['status' => 'approved', 'active' => 2])) {
        // Get display name
        try {
            $info = $this->userInfoModel->getByUserId($id) ?? [];
        } catch (\Throwable $_) {
            $info = [];
        }
        $fullName = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? ''));
        $displayName = $fullName ?: ($user['email'] ?? 'User');

        // Prepare email values (escaped)
        $siteUrl = rtrim(base_url(), '/');
        $loginUrl = $siteUrl . '/login';
        $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
        $fromName  = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';
        $displayNameEsc = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
        $loginUrlEsc = htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8');
        $fromNameEsc = htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8');

        // Send approval email (non-blocking)
        try {
            $apiKey = getenv('BREVO_API_KEY') ?: null;
            if ($apiKey) {
                $config = \Brevo\Client\Configuration::getDefaultConfiguration()
                    ->setApiKey('api-key', $apiKey);
                $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

                $subject = 'Your account has been approved';

                $html = <<<HTML
    <!doctype html>
    <html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    </head>
    <body style="margin:0;padding:0;background:#f5f7fa;font-family:Poppins,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
        <td align="center" style="padding:24px 12px;">
            <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 18px rgba(16,24,40,0.08);">
            <tr>
                <td style="padding:28px 32px 16px;">
                <h2 style="margin:0 0 8px;font-weight:600;color:#111827;font-size:20px;">Account Approved</h2>
                <p style="margin:0;color:#6b7280;">Hello {$displayNameEsc},</p>
                </td>
            </tr>
            <tr>
                <td style="padding:0 32px 24px;color:#374151;">
                <p style="margin:0 0 12px;line-height:1.5;">We are pleased to let you know that your account has been approved by our administrator. You can now sign in using your registered email and password.</p>
                <p style="margin:0 0 20px;">
                    <a href="{$loginUrlEsc}" style="display:inline-block;padding:10px 18px;background:#2563eb;color:#ffffff;border-radius:6px;text-decoration:none;font-weight:600;">Sign in to your account</a>
                </p>
                <p style="margin:0;color:#9ca3af;font-size:12px;">If the button above does not work, copy and paste this link into your browser: {$loginUrlEsc}</p>
                </td>
            </tr>
            <tr>
                <td style="padding:16px 32px 28px;background:#f9fafb;color:#6b7280;font-size:12px;">
                <div style="margin-bottom:6px;">Best regards,</div>
                <div style="font-weight:600;color:#111827;">{$fromNameEsc}</div>
                <div style="margin-top:8px;font-size:11px;color:#9ca3af;">This is an automated message. Please do not reply directly to this email.</div>
                </td>
            </tr>
            </table>

            <div style="max-width:600px;margin-top:12px;color:#9ca3af;font-size:12px;text-align:center;">
            &copy; {$siteUrl} ' . date('Y') . '
            </div>
        </td>
        </tr>
    </table>
    </body>
    </html>
    HTML;

                $plain = "Hello {$displayName},\n\nYour account has been approved. You can sign in at: {$loginUrl}\n\nThis is an automated message.";

                $email = new \Brevo\Client\Model\SendSmtpEmail([
                    'subject' => $subject,
                    'sender' => ['name' => $fromName, 'email' => $fromEmail],
                    'to' => [['email' => $user['email'], 'name' => $displayName]],
                    'htmlContent' => $html,
                    'textContent' => $plain
                ]);

                $apiInstance->sendTransacEmail($email);
            } else {
                log_message('warning', 'BREVO_API_KEY not configured — skipping approval email for user ' . ($user['email'] ?? 'unknown'));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Approval email failed for user ' . ($user['email'] ?? 'unknown') . ': ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'User approved successfully.');
    }

    return redirect()->back()->with('error', 'Failed to approve user.');
}
    // Reject user account
    public function reject($id)
    {
        if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

        if ($this->usersModel->update($id, ['status' => 'rejected'])) {
            return redirect()->back()->with('success', 'User rejected successfully.');
        }

        return redirect()->back()->with('error', 'Failed to reject user.');
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
    
    // Reject GCash Payment (AJAX) - Transaction Payments
    public function rejectGCashPayment()
    {
        $data = $this->request->getJSON(true);
        $paymentId = $data['payment_id'] ?? null;
        $adminRef = $data['admin_reference'] ?? null;

        if (!$paymentId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing payment_id']);
        }

        try {
            $updated = $this->paymentsModel->rejectGCashPayment($paymentId, $adminRef);
            return $this->response->setJSON([
                'success' => (bool)$updated,
                'message' => $updated ? 'Payment rejected successfully' : 'Failed to reject payment'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }

    // Get users by Purok (AJAX) - Transaction Payments
    public function getUsersByPurok($purok)
    {
        try {
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
                    // ✅ FIX: Use CodeIgniter 4 compatible subquery syntax
                    $db = \Config\Database::connect();
                    $subquery = $db->table('billings')
                        ->select('user_id')
                        ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
                        ->getCompiledSelect();
                    
                    // Use whereNotIn with the compiled subquery
                    $builder->where("user_information.user_id NOT IN ($subquery)", null, false);
                    
                    log_message('info', "getUsersByPurok - Filtering out users with existing billings for month: {$month}");
                }
            }
            
            $users = $builder->findAll();
            
            // More detailed debug logging
            log_message('info', "getUsersByPurok Result - Found " . count($users) . " users");
            foreach ($users as $user) {
                log_message('info', "User: ID {$user['user_id']}, Name: {$user['first_name']} {$user['last_name']}");
            }
            
            return $this->response->setJSON($users);
            
        } catch (\Exception $e) {
            log_message('error', "getUsersByPurok error: " . $e->getMessage());
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ]);
        }
    }


    // Get pending billings for a user (AJAX) - transaction Payments
    public function getPendingBillings($userId)
    {
        try {
            $month = $this->request->getGet('month');
            $billingModel = new \App\Models\BillingModel();

            // Add debug logging
            log_message('info', "getPendingBillings - UserID: {$userId}, Month: {$month}");

            // Validate user ID
            if (!$userId || !is_numeric($userId)) {
                log_message('error', "Invalid user ID provided: {$userId}");
                return $this->response->setJSON([
                    'error' => true,
                    'message' => 'Invalid user ID'
                ]);
            }

            // Validate user exists
            $userModel = new \App\Models\UsersModel();
            $user = $userModel->find($userId);
            if (!$user) {
                log_message('error', "User not found: {$userId}");
                return $this->response->setJSON([
                    'error' => true,
                    'message' => 'User not found'
                ]);
            }

            $billings = $billingModel->getPendingBillingsByUserAndMonth($userId, $month);
            
            // Add debug logging for results
            log_message('info', "getPendingBillings - Found " . count($billings) . " billings for specific month");
            
            if (empty($billings) && !empty($month)) {
                // If no billings for specific month, try to get any pending billings for this user
                log_message('info', "No billings for month {$month}, trying all pending billings for user {$userId}");
                $billings = $billingModel->getPendingBillingsByUserAndMonth($userId, null);
                log_message('info', "Found " . count($billings) . " total pending billings for user");
            }

            // Ensure we return a proper array structure
            $formattedBillings = [];
            foreach ($billings as $billing) {
                $formattedBillings[] = [
                    'id' => $billing['id'],
                    'bill_no' => $billing['bill_no'] ?? 'BILL-' . $billing['id'],
                    'amount_due' => (float)$billing['amount_due'],
                    'status' => $billing['status'],
                    'billing_month' => $billing['billing_month'],
                    'due_date' => $billing['due_date'],
                    'created_at' => $billing['created_at']
                ];
            }

            log_message('info', "getPendingBillings - Returning " . count($formattedBillings) . " formatted billings");

            return $this->response->setJSON($formattedBillings);

        } catch (\Exception $e) {
            log_message('error', "getPendingBillings error: " . $e->getMessage());
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Failed to fetch billings: ' . $e->getMessage()
            ]);
        }
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

    // Export current reports data (CSV/Excel/PDF/Print)
    public function exportReports()
    {
        $format = strtolower($this->request->getGet('format') ?? 'csv');
        $fileBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $this->request->getGet('filename') ?? ('reports_' . date('Y-m-d')));
        if (!$fileBase) { $fileBase = 'reports_' . date('Y-m-d'); }

        // Optional filters to match on-screen state
        $start = trim((string)$this->request->getGet('start'));
        $end = trim((string)$this->request->getGet('end'));
        $type = trim((string)$this->request->getGet('type'));
        $defaultStart = date('Y') . '-01-01';
        $defaultEnd = date('Y-m-d');
        $startDate = (\DateTime::createFromFormat('Y-m-d', $start) && $start === \DateTime::createFromFormat('Y-m-d', $start)->format('Y-m-d')) ? $start : $defaultStart;
        $endDate = (\DateTime::createFromFormat('Y-m-d', $end) && $end === \DateTime::createFromFormat('Y-m-d', $end)->format('Y-m-d')) ? $end : $defaultEnd;
        if ($endDate < $startDate) { $endDate = $startDate; }
        $year = date('Y');
        $monthNow = date('m');

        // Household distribution (align with reports())
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

        $totalHouseholds = (int)($normalCount + $seniorCount + $aloneCount);

        // Fixed rates
        $rateNormal = 60; $rateSenior = 48; $rateAlone = 30;

        // Payment status (within selected range)
        $paidHouseholds = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('status', 'Paid')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->get()->getRow()->c ?? 0);

        $pendingCount = (int)($this->billingModel
            ->where('status', 'Pending')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->countAllResults());

        $latePayments = (int)$this->billingModel
            ->where('status', 'Pending')
            ->where('due_date <', date('Y-m-d'))
            ->where('DATE(due_date) >=', $startDate)
            ->where('DATE(due_date) <=', $endDate)
            ->countAllResults();

        // Monthly amounts and collection rate in selected window (paid unique households / total)
        $monthlyAmounts = array_fill(1, 12, 0.0);
        $monthlyRates = array_fill(1, 12, 0.0);
        // Aggregate by month within the date window
        $rows = $this->billingModel
            ->select("YEAR(updated_at) as y, MONTH(updated_at) as m, SUM(amount_due) as amt, COUNT(DISTINCT user_id) as pc")
            ->where('status', 'Paid')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->groupBy('YEAR(updated_at), MONTH(updated_at)')
            ->orderBy('YEAR(updated_at)', 'ASC')
            ->orderBy('MONTH(updated_at)', 'ASC')
            ->get()->getResultArray();
        foreach ($rows as $r) {
            $m = (int)$r['m'];
            $monthlyAmounts[$m] = (float)($r['amt'] ?? 0);
            $paidC = (int)($r['pc'] ?? 0);
            $monthlyRates[$m] = $totalHouseholds > 0 ? round(($paidC / $totalHouseholds) * 100, 1) : 0.0;
        }

        // Ensure collection summary values exist for printable/export views
        $currentMonthCollected = (float)($this->billingModel
            ->where('status', 'Paid')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0);

        $pendingAmount = (float)($this->billingModel
            ->where('status', 'Pending')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0);

        $collectionRate = $totalHouseholds > 0 ? round(($paidHouseholds / $totalHouseholds) * 100, 1) : 0.0;

        // Prefetch users and paid billings within the selected date window to avoid per-user queries
        try {
            $users = $this->userInfoModel
                ->select('user_information.purok, user_information.user_id, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
                ->join('users', 'users.id = user_information.user_id', 'left')
                ->whereIn('users.status', ['approved','Approved'])
                ->orderBy('user_information.purok', 'ASC')
                ->orderBy('user_information.first_name', 'ASC')
                ->findAll();

            $billingsInRange = $this->billingModel
                ->select('user_id, updated_at, amount_due, status')
                ->where('DATE(updated_at) >=', $startDate)
                ->where('DATE(updated_at) <=', $endDate)
                ->where('status', 'Paid')
                ->orderBy('user_id','ASC')
                ->findAll();

            $paidByUser = [];
            foreach ($billingsInRange as $b) {
                $paidByUser[(int)$b['user_id']][] = $b;
            }
        } catch (\Throwable $_) {
            $users = [];
            $paidByUser = [];
        }

        if ($format === 'pdf' || $format === 'print') {
            // Render only the requested report unless type is empty (Export All -> combined)
            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $period = ($startDate === ($year.'-01-01') && $endDate === date('Y-m-d')) ? $year : ($startDate . ' to ' . $endDate);

            // Helper: render summary HTML (compact fixed-bill summary with per-rate calculations)
            $renderSummary = function() use ($year, $normalCount, $seniorCount, $aloneCount, $rateNormal, $rateSenior, $rateAlone) {
                $t = 'Fixed Bill Summary';
                $normalTotal = $normalCount * $rateNormal;
                $seniorTotal = $seniorCount * $rateSenior;
                $aloneTotal = $aloneCount * $rateAlone;
                $expected = $normalTotal + $seniorTotal + $aloneTotal;

                $h = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($t) . '</title>' .
                    '<style>body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:24px;}h1{margin:0 0 6px;font-size:20px}h2{margin:12px 0 6px;}table{width:100%;margin:8px 0;border-collapse:collapse}td{padding:6px;font-size:14px} .muted{color:#6b7280}</style>' .
                    '</head><body>';
                $h .= '<h1>' . htmlspecialchars($t) . '</h1>';
                $h .= '<div class="muted">Year ' . intval($year) . ' Overview</div>';

                $h .= '<div style="margin-top:12px;">';
                $h .= '<div><strong>Normal Rate (' . intval($normalCount) . ' households)</strong></div>';
                $h .= '<div>₱' . number_format($rateNormal,2) . ' * ' . intval($normalCount) . ' = ₱' . number_format($normalTotal,2) . '</div>';

                $h .= '<div style="margin-top:8px"><strong>Senior Citizen Rate (' . intval($seniorCount) . ' households)</strong></div>';
                $h .= '<div>₱' . number_format($rateSenior,2) . ' * ' . intval($seniorCount) . ' = ₱' . number_format($seniorTotal,2) . '</div>';

                $h .= '<div style="margin-top:8px"><strong>Living Alone Rate (' . intval($aloneCount) . ' households)</strong></div>';
                $h .= '<div>₱' . number_format($rateAlone,2) . ' * ' . intval($aloneCount) . ' = ₱' . number_format($aloneTotal,2) . '</div>';

                $h .= '<div style="margin-top:12px; font-weight:700">Expected Monthly Collection</div>';
                $h .= '<div>₱' . number_format($expected,2) . '</div>';
                $h .= '</div>';
                $h .= '</body></html>';
                return $h;
            };

            // Helper: render collection detail per purok (with top-level collection stats)
            $renderCollection = function($users, $paidByUser, $startDate, $endDate) use ($currentMonthCollected, $paidHouseholds, $totalHouseholds, $pendingAmount, $collectionRate) {
                $title = 'Payment Collection';
                $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title>' .
                    '<style>body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:24px;}h1{margin:0 0 6px;}table{border-collapse:collapse;width:100%;margin:8px 0;}th,td{border:1px solid #ddd;padding:8px;font-size:12px}th{background:#f3f4f6;text-align:left}.purok{margin-top:18px;margin-bottom:6px;font-weight:700}.stats{display:flex;gap:12px;margin:8px 0}</style>' .
                    '</head><body>';
                $html .= '<h1>' . htmlspecialchars($title) . '</h1>';
                $html .= '<div class="stats">';
                $html .= '<div><div style="font-weight:700">Collected This Month</div><div>₱' . number_format($currentMonthCollected,2) . '</div></div>';
                $html .= '<div><div style="font-weight:700">Paid Households</div><div>' . intval($paidHouseholds) . ' of ' . intval($totalHouseholds) . '</div></div>';
                $html .= '<div><div style="font-weight:700">Pending Collection</div><div>₱' . number_format($pendingAmount,2) . '</div></div>';
                $html .= '<div><div style="font-weight:700">Collection Rate</div><div>' . number_format($collectionRate,1) . '%</div></div>';
                $html .= '</div>';
                $html .= '<div class="muted">Period: ' . htmlspecialchars($startDate . ' to ' . $endDate) . '</div>';

                $byPurok = [];
                foreach ($users as $u) {
                    $p = $u['purok'] ?? 'Unspecified';
                    if ($p === '' || $p === null) $p = 'Unspecified';
                    $byPurok[$p][] = $u;
                }

                foreach ($byPurok as $purok => $plist) {
                    $html .= '<div class="purok">Purok: ' . htmlspecialchars($purok) . '</div>';
                    $html .= '<table><thead><tr><th>User ID</th><th>Name</th><th>Paid?</th><th>Paid Dates</th><th>Amounts</th></tr></thead><tbody>';
                    foreach ($plist as $u) {
                        $uid = (int)$u['user_id'];
                        $paidRows = $paidByUser[$uid] ?? [];
                        if (!empty($paidRows)) {
                            $paid = 'Yes';
                            $dates = array_map(function($r){ return $r['updated_at']; }, $paidRows);
                            $amounts = array_map(function($r){ return number_format($r['amount_due'],2); }, $paidRows);
                            $dateStr = implode('; ', $dates);
                            $amtStr = implode('; ', $amounts);
                        } else {
                            $paid = 'No';
                            $dateStr = '';
                            $amtStr = '';
                        }
                        $html .= '<tr><td>' . $uid . '</td><td>' . htmlspecialchars($u['name']) . '</td><td>' . $paid . '</td><td>' . htmlspecialchars($dateStr) . '</td><td>' . htmlspecialchars($amtStr) . '</td></tr>';
                    }
                    $html .= '</tbody></table>';
                }

                $html .= '</body></html>';
                return $html;
            };

            // Route rendering based on requested type
            if ($type === '') {
                // Export All: combined summary + per-purok
                $html = $renderSummary();
                // Append per-purok details using prefetched data
                $html .= substr($renderCollection($users, $paidByUser, $startDate, $endDate), 14); // strip DOCTYPE to concatenate simple
                return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
            }

            if ($type === 'summary') {
                $html = $renderSummary();
                return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
            }

            if ($type === 'collection') {
                $html = $renderCollection($users, $paidByUser, $startDate, $endDate);
                return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
            }

            if ($type === 'distribution') {
                // Distribution: render rate distribution chart/table + counts
                $title = 'Rate Distribution (Households)';
                $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title>' .
                    '<style>body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:24px;}table{border-collapse:collapse;width:100%;margin:8px 0;}th,td{border:1px solid #ddd;padding:8px;font-size:12px}th{background:#f3f4f6;text-align:left}</style></head><body>';
                $html .= '<h1>' . htmlspecialchars($title) . '</h1>';
                $html .= '<table><thead><tr><th>Category</th><th>Households</th></tr></thead><tbody>';
                $html .= '<tr><td>Normal</td><td>' . (int)$normalCount . '</td></tr>';
                $html .= '<tr><td>Senior</td><td>' . (int)$seniorCount . '</td></tr>';
                $html .= '<tr><td>Alone</td><td>' . (int)$aloneCount . '</td></tr>';
                $html .= '<tr><td>Total</td><td>' . (int)$totalHouseholds . '</td></tr>';
                $html .= '</tbody></table>';
                $html .= '</body></html>';
                return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
            }

            if ($type === 'community') {
                $title = 'Community Statistics';
                $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title>' .
                    '<style>body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:24px;}table{border-collapse:collapse;width:100%;margin:8px 0;}th,td{border:1px solid #ddd;padding:8px;font-size:12px}th{background:#f3f4f6;text-align:left}</style></head><body>';
                $html .= '<h1>' . htmlspecialchars($title) . '</h1>';
                $html .= '<table><tbody>';
                $html .= '<tr><th>Active Households</th><td>' . (int)$totalHouseholds . '</td></tr>';
                $html .= '<tr><th>Pending Payments</th><td>' . (int)$pendingCount . '</td></tr>';
                $html .= '<tr><th>Payment Compliance (%)</th><td>' . (float)$collectionRate . '</td></tr>';
                $html .= '<tr><th>Late Payments</th><td>' . (int)$latePayments . '</td></tr>';
                $html .= '</tbody></table>';
                $html .= '</body></html>';
                return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
            }

            // Default fallback: render summary
            $html = $renderSummary();
            return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
        }

        // If Excel requested and PhpSpreadsheet is available, generate native .xlsx with a chart
        if (($format === 'excel' || $format === 'xlsx') && class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            // Use PhpSpreadsheet to build a workbook. If exporting collection detail, produce one sheet per purok.
            $spreadsheet = new Spreadsheet();

            if ($type === 'collection' || $type === '') {
                // If type is blank (Export All) or specifically collection, create per-purok sheets plus a summary sheet.
                // Summary sheet first
                $sum = $spreadsheet->getActiveSheet();
                $sum->setTitle('Summary');
                $sum->fromArray([
                    ['Metric', 'Value'],
                    ['Paid Households', $paidHouseholds],
                    ['Pending', $pendingCount],
                    ['Late', $latePayments],
                    ['Normal Households', $normalCount],
                    ['Senior Households', $seniorCount],
                    ['Alone Households', $aloneCount],
                    ['Total Households', $totalHouseholds],
                    ['Rate Normal (PHP)', $rateNormal],
                    ['Rate Senior (PHP)', $rateSenior],
                    ['Rate Alone (PHP)', $rateAlone],
                ], null, 'A1');

                // Build per-purok sheets using prefetched $users and $paidByUser
                $byPurok = [];
                foreach ($users as $u) {
                    $p = $u['purok'] ?? 'Unspecified';
                    if ($p === '' || $p === null) $p = 'Unspecified';
                    $byPurok[$p][] = $u;
                }

                // Summary sheet is the first (index 0)
                $sum->setTitle('Summary');

                // Create one sheet per purok and populate rows
                foreach ($byPurok as $purok => $plist) {
                    // Create a new sheet for each purok
                    $sheet = $spreadsheet->createSheet();
                    // Excel sheet titles are limited to 31 chars
                    $title = 'Purok ' . $purok;
                    $sheet->setTitle(substr($title, 0, 31));

                    // Header row
                    $sheet->fromArray(['User ID','Name','Paid?','Paid Dates','Amounts (PHP)'], null, 'A1');
                    $r = 2;
                    foreach ($plist as $u) {
                        $uid = (int)$u['user_id'];
                        $paidRows = $paidByUser[$uid] ?? [];
                        if (!empty($paidRows)) {
                            $paid = 'Yes';
                            $dates = array_map(function($b){ return $b['updated_at']; }, $paidRows);
                            $amounts = array_map(function($b){ return number_format($b['amount_due'],2,'.',''); }, $paidRows);
                            $dateStr = implode('; ', $dates);
                            $amtStr = implode('; ', $amounts);
                        } else {
                            $paid = 'No';
                            $dateStr = '';
                            $amtStr = '';
                        }
                        $sheet->setCellValue('A'.$r, $uid);
                        $sheet->setCellValue('B'.$r, $u['name'] ?? 'Unknown');
                        $sheet->setCellValue('C'.$r, $paid);
                        $sheet->setCellValue('D'.$r, $dateStr);
                        $sheet->setCellValue('E'.$r, $amtStr);
                        $r++;
                    }
                }
                // Build a small per-purok summary table on the Summary sheet and add a chart
                $spreadsheet->setActiveSheetIndex(0);
                $sum = $spreadsheet->getActiveSheet();

                // Determine starting row for the Purok table (place after existing metrics)
                $metricsRows = 11; // number of metric rows written earlier
                $tableHeaderRow = $metricsRows + 3; // leave a blank row
                $r = $tableHeaderRow;
                $sum->setCellValue('A' . $r, 'Purok');
                $sum->setCellValue('B' . $r, 'Total Households');
                $sum->setCellValue('C' . $r, 'Paid Households');
                $sum->setCellValue('D' . $r, 'Collection Rate (%)');

                $r++;
                $purokCount = 0;
                foreach ($byPurok as $purok => $plist) {
                    $total = count($plist);
                    $paid = 0;
                    foreach ($plist as $u) {
                        $uid = (int)$u['user_id'];
                        if (!empty($paidByUser[$uid])) $paid++;
                    }
                    $rate = $total > 0 ? round(($paid / $total) * 100, 1) : 0;
                    $sum->setCellValue('A' . $r, (string)$purok);
                    $sum->setCellValue('B' . $r, $total);
                    $sum->setCellValue('C' . $r, $paid);
                    $sum->setCellValue('D' . $r, $rate);
                    $r++;
                    $purokCount++;
                }

                // If there is at least one purok, build a bar chart of Collection Rate (%) per purok
                if ($purokCount > 0) {
                    $firstDataRow = $tableHeaderRow + 1;
                    $lastDataRow = $tableHeaderRow + $purokCount;

                    // Build DataSeriesValues using A (categories) and D (values) columns on the Summary sheet
                    $catRef = "'" . $sum->getTitle() . "'!\$A\$" . $firstDataRow . ":\$A\$" . $lastDataRow;
                    $valRef = "'" . $sum->getTitle() . "'!\$D\$" . $firstDataRow . ":\$D\$" . $lastDataRow;

                    $categories = new DataSeriesValues('String', $catRef, null, $purokCount);
                    $values = new DataSeriesValues('Number', $valRef, null, $purokCount);

                    $series = new DataSeries(
                        DataSeries::TYPE_BARCHART,
                        null,
                        [0],
                        [],
                        [$categories],
                        [$values]
                    );

                    $plotArea = new PlotArea(null, [$series]);
                    $title = new Title('Collection Rate by Purok (%)');
                    $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

                    $chart = new XlsChart('purok_chart', $title, $legend, $plotArea, true, 0, null, null);
                    // Position the chart to the right of the table
                    $chart->setTopLeftPosition('F2');
                    $chart->setBottomRightPosition('N20');
                    $sum->addChart($chart);
                }

                $filename = $fileBase . '.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->setIncludeCharts(true);
                $writer->save('php://output');
                exit;
            }

            // Fallback: existing monthly sheet behavior for other types
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Monthly');

            // Headers
            $sheet->setCellValue('A1', 'Month');
            $sheet->setCellValue('B1', 'Collection Rate (%)');
            $sheet->setCellValue('C1', 'Amount (PHP)');

            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            for ($i = 1; $i <= 12; $i++) {
                $row = $i + 1;
                $sheet->setCellValue('A' . $row, $months[$i-1]);
                $sheet->setCellValue('B' . $row, (float)$monthlyRates[$i]);
                $sheet->setCellValue('C' . $row, (float)$monthlyAmounts[$i]);
            }

            // Summary sheet
            $sum = $spreadsheet->createSheet();
            $sum->setTitle('Summary');
            $sum->fromArray([
                ['Metric', 'Value'],
                ['Paid Households', $paidHouseholds],
                ['Pending', $pendingCount],
                ['Late', $latePayments],
                ['Normal Households', $normalCount],
                ['Senior Households', $seniorCount],
                ['Alone Households', $aloneCount],
                ['Total Households', $totalHouseholds],
                ['Rate Normal (PHP)', $rateNormal],
                ['Rate Senior (PHP)', $rateSenior],
                ['Rate Alone (PHP)', $rateAlone],
            ], null, 'A1');

            // Output to browser
            $filename = $fileBase . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);
            $writer->save('php://output');
            exit;
        }

        // Special CSV/Excel export for collection detail (per-purok)
        if ($type === 'collection') {
            $filename = $fileBase . ($format === 'excel' ? '.xls' : '.csv');
            if ($format === 'excel') {
                header('Content-Type: application/vnd.ms-excel');
            } else {
                header('Content-Type: text/csv');
            }
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $out = fopen('php://output', 'w');
            // Header for collection detail
            fputcsv($out, ['Purok', 'User ID', 'Name', 'Paid?', 'Paid Dates', 'Amounts (PHP)']);

            // Fetch users grouped by purok
            $users = $this->userInfoModel
                ->select('user_information.purok, user_information.user_id, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
                ->join('users', 'users.id = user_information.user_id', 'left')
                ->whereIn('users.status', ['approved','Approved'])
                ->orderBy('user_information.purok', 'ASC')
                ->orderBy('user_information.first_name', 'ASC')
                ->findAll();

            foreach ($users as $u) {
                $purok = $u['purok'] ?? 'Unspecified';
                if ($purok === '' || $purok === null) $purok = 'Unspecified';
                $uid = (int)$u['user_id'];
                $paidRows = $this->billingModel
                    ->where('user_id', $uid)
                    ->where('status', 'Paid')
                    ->where('DATE(updated_at) >=', $startDate)
                    ->where('DATE(updated_at) <=', $endDate)
                    ->orderBy('updated_at','ASC')
                    ->findAll();
                if (!empty($paidRows)) {
                    $paid = 'Yes';
                    $dates = array_map(function($r){ return $r['updated_at']; }, $paidRows);
                    $amounts = array_map(function($r){ return number_format($r['amount_due'],2,'.',''); }, $paidRows);
                    $dateStr = implode('; ', $dates);
                    $amtStr = implode('; ', $amounts);
                } else {
                    $paid = 'No';
                    $dateStr = '';
                    $amtStr = '';
                }
                fputcsv($out, [$purok, $uid, ($u['name'] ?? 'Unknown'), $paid, $dateStr, $amtStr]);
            }

            fclose($out);
            exit;
        }

        // Prepare CSV (also used for legacy Excel compatibility)
        $filename = $fileBase . ($format === 'excel' ? '.xls' : '.csv');
        if ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
        } else {
            header('Content-Type: text/csv');
        }
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        // Monthly Collection
        fputcsv($out, ['Section', 'Monthly Collection (' . $year . ')']);
        fputcsv($out, ['Month', 'Collection Rate (%)', 'Amount (PHP)']);
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        for ($i = 1; $i <= 12; $i++) {
            fputcsv($out, [$months[$i-1], $monthlyRates[$i], number_format($monthlyAmounts[$i], 2, '.', '')]);
        }
        fputcsv($out, []);

        // Payment Status (selected range)
        fputcsv($out, ['Section', 'Payment Status (Selected Range)']);
        fputcsv($out, ['Paid Households', 'Pending', 'Late']);
        fputcsv($out, [$paidHouseholds, $pendingCount, $latePayments]);
        fputcsv($out, []);

        // Rate Distribution (households)
        fputcsv($out, ['Section', 'Rate Distribution (Households)']);
        fputcsv($out, ['Normal', 'Senior', 'Alone', 'Total Households']);
        fputcsv($out, [$normalCount, $seniorCount, $aloneCount, $totalHouseholds]);
        fputcsv($out, []);

        // Fixed Rates
        fputcsv($out, ['Section', 'Fixed Rates (PHP)']);
        fputcsv($out, ['Normal', 'Senior', 'Alone']);
        fputcsv($out, [number_format($rateNormal,2,'.',''), number_format($rateSenior,2,'.',''), number_format($rateAlone,2,'.','')]);

        // If exporting collection details or exporting All, append per-purok detail rows (tables)
        if ($type === '' || $type === 'collection') {
            fputcsv($out, []);
            fputcsv($out, ['Section', 'Collection Details (Per Purok)']);
            fputcsv($out, ['Purok', 'User ID', 'Name', 'Paid?', 'Paid Dates', 'Amounts (PHP)']);

            // Fetch users grouped by purok
            $users = $this->userInfoModel
                ->select('user_information.purok, user_information.user_id, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
                ->join('users', 'users.id = user_information.user_id', 'left')
                ->whereIn('users.status', ['approved','Approved'])
                ->orderBy('user_information.purok', 'ASC')
                ->orderBy('user_information.first_name', 'ASC')
                ->findAll();

            foreach ($users as $u) {
                $purok = $u['purok'] ?? 'Unspecified';
                if ($purok === '' || $purok === null) $purok = 'Unspecified';
                $uid = (int)$u['user_id'];
                $paidRows = $this->billingModel
                    ->where('user_id', $uid)
                    ->where('status', 'Paid')
                    ->where('DATE(updated_at) >=', $startDate)
                    ->where('DATE(updated_at) <=', $endDate)
                    ->orderBy('updated_at','ASC')
                    ->findAll();
                if (!empty($paidRows)) {
                    $paid = 'Yes';
                    $dates = array_map(function($r){ return $r['updated_at']; }, $paidRows);
                    $amounts = array_map(function($r){ return number_format($r['amount_due'],2,'.',''); }, $paidRows);
                    $dateStr = implode('; ', $dates);
                    $amtStr = implode('; ', $amounts);
                } else {
                    $paid = 'No';
                    $dateStr = '';
                    $amtStr = '';
                }
                fputcsv($out, [$purok, $uid, ($u['name'] ?? 'Unknown'), $paid, $dateStr, $amtStr]);
            }
        }

        fclose($out);
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
                new GuzzleClient(),
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

        // Read filters (YYYY-MM-DD). Defaults: start = Jan 1 of current year, end = today
        $start = trim((string)$this->request->getGet('start'));
        $end = trim((string)$this->request->getGet('end'));
        $type = trim((string)$this->request->getGet('type'));

        $defaultStart = date('Y') . '-01-01';
        $defaultEnd = date('Y-m-d');

        $startDate = (\DateTime::createFromFormat('Y-m-d', $start) && $start === \DateTime::createFromFormat('Y-m-d', $start)->format('Y-m-d')) ? $start : $defaultStart;
        $endDate = (\DateTime::createFromFormat('Y-m-d', $end) && $end === \DateTime::createFromFormat('Y-m-d', $end)->format('Y-m-d')) ? $end : $defaultEnd;
        if ($endDate < $startDate) { $endDate = $startDate; }
        
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
        
        // Collection within selected date range
        $currentMonthCollected = $this->billingModel
            ->where('status', 'Paid')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0;
        
        // Paid households in range (distinct users), based on updated_at
        $paidHouseholds = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('status', 'Paid')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->get()
            ->getRow()->c ?? 0);
        
        // Pending amount and count (range)
        $pendingAmount = (float)($this->billingModel
            ->where('status', 'Pending')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0);

        $pendingCount = (int)($this->billingModel
            ->where('status', 'Pending')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->countAllResults());
        
        // Late payments (overdue)
        $latePayments = $this->billingModel
            ->where('status', 'Pending')
            ->where('due_date <', date('Y-m-d'))
            ->where('DATE(due_date) >=', $startDate)
            ->where('DATE(due_date) <=', $endDate)
            ->countAllResults();
        
        // If no data for current month, fallback to Year-To-Date for status overview
        $statusScope = 'RANGE';

        // Collection rate
        $collectionRate = $totalHouseholds > 0 ? round(($paidHouseholds / $totalHouseholds) * 100, 1) : 0;
        
        // Monthly collection rates and amounts for chart (within range)
        $monthlyData = $this->billingModel
            ->select("YEAR(updated_at) as y, MONTH(updated_at) as month_num, COUNT(DISTINCT user_id) as paid_count, SUM(amount_due) as total_amount")
            ->where('status', 'Paid')
            ->where('DATE(updated_at) >=', $startDate)
            ->where('DATE(updated_at) <=', $endDate)
            ->groupBy('YEAR(updated_at), MONTH(updated_at)')
            ->orderBy('YEAR(updated_at)', 'ASC')
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

        // Only apply rolling fallback when no explicit filters were provided
        $filtersProvided = ($this->request->getGet('start') || $this->request->getGet('end') || $this->request->getGet('type'));
        if (!$filtersProvided && array_sum($collectionAmounts) == 0) {
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
            'statusScope' => $statusScope,
            // Expose filters to the views
            'filterStart' => $startDate,
            'filterEnd' => $endDate,
            'filterType' => $type,
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
        // Remove any stale inactive_users snapshots for this user
        try {
            $db = \Config\Database::connect();
            $db->table('inactive_users')->where('user_id', $id)->delete();
        } catch (\Throwable $_) {
            // ignore cleanup errors
        }
        // Persist friendly display name into the activity log to guarantee history
        try {
            $logId = session()->get('admin_activity_log_id') ?? session()->get('superadmin_activity_log_id');
            if ($logId) {
                $user = $this->usersModel->find($id);
                $info = $this->userInfoModel->getByUserId($id) ?? [];
                $display = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? '')) ?: ($user['email'] ?? null);
                $details = [
                    'id' => $id,
                    'user_name' => $display,
                    'first_name' => $info['first_name'] ?? null,
                    'last_name' => $info['last_name'] ?? null,
                    'email' => $user['email'] ?? null,
                ];

                // mark to skip the ActivityLogger to avoid duplicate entry
                session()->set('skip_activity_logger', true);
                $logModel = new \App\Models\AdminActivityLogModel();
                $logModel->appendAction($logId, 'activate', '/admin/activateUser/' . $id, $this->request->getMethod() ?: 'POST', $id, $details);
            }
        } catch (\Throwable $_) {
            // ignore logging failures
        }

        return redirect()->back()->with('success', 'User activated successfully.');
    }

    public function deactivateUser($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $user = $this->usersModel->find($id);
        if (!$user) {
            if ($this->request->isAJAX() || $this->request->is('post')) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
            }
            return redirect()->back()->with('error', 'User not found.');
        }

        // Block deactivation if user has outstanding balance/pending bills
        try {
            $outstanding = $this->billingModel
                ->where('user_id', $id)
                ->where('status', 'Pending')
                ->countAllResults();
            log_message('debug', '[Admin::deactivateUser] outstanding count for user ' . $id . ' => ' . $outstanding);
            if ($outstanding > 0) {
                try {
                    $samples = $this->billingModel->select('id, bill_no, status')->where('user_id', $id)->where('status', 'Pending')->findAll(5);
                    $ids = array_map(fn($b)=> $b['id'], $samples);
                    log_message('debug', '[Admin::deactivateUser] sample pending bill ids for user ' . $id . ': ' . json_encode($ids));
                } catch (\Throwable $_) {
                    // ignore
                }
            }
        } catch (\Throwable $e) {
            $outstanding = 0; // Fail open on count error to avoid false positives
        }
        if ($outstanding > 0) {
            $db->transRollback();
            $msg = 'Cannot deactivate: user has pending bill(s). Please settle or cancel pending bills first.';
            if ($this->request->isAJAX() || $this->request->is('post')) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $info = $this->userInfoModel->getByUserId($id) ?? [];
        $now = date('Y-m-d H:i:s');
        $adminId = (int)(session()->get('admin_id') ?? 0);

        // 1) Update user status/active
        $this->usersModel->update($id, ['active' => 1, 'status' => 'inactive']);

        // 2) Insert snapshot into inactive_users and capture the insert id
        $inactiveData = [
            'user_id'        => $id,
            'email'          => $user['email'] ?? '',
            'first_name'     => $info['first_name'] ?? null,
            'last_name'      => $info['last_name'] ?? null,
            'phone'          => $info['phone'] ?? null,
            'purok'          => isset($info['purok']) ? (int)$info['purok'] : null,
            'barangay'       => $info['barangay'] ?? 'Borlongan',
            'municipality'   => $info['municipality'] ?? 'Dipaculao',
            'province'       => $info['province'] ?? 'Aurora',
            'zipcode'        => $info['zipcode'] ?? '3203',
            'inactivated_at' => $now,
            'inactivated_by' => $adminId ?: null,
            'reason'         => $this->request->getPost('reason') ?? null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ];

        try {
            $db->table('inactive_users')->insert($inactiveData);
            $inactiveId = (int)$db->insertID();
        } catch (\Throwable $e) {
            // If insertion fails, rollback and return error
            $db->transRollback();
            if ($this->request->isAJAX() || $this->request->is('post')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to persist inactive user snapshot.']);
            }
            return redirect()->back()->with('error', 'Failed to persist inactive user snapshot.');
        }

        // Append action with display name into activity log (controller-level)
        try {
            $logId = session()->get('admin_activity_log_id') ?? session()->get('superadmin_activity_log_id');
            if ($logId) {
                $display = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? '')) ?: ($user['email'] ?? null);
                $details = [
                    'id' => $id,
                    'user_name' => $display,
                    'first_name' => $info['first_name'] ?? null,
                    'last_name' => $info['last_name'] ?? null,
                    'email' => $user['email'] ?? null,
                    'reason' => $this->request->getPost('reason') ?? null,
                    'inactive_ref_id' => $inactiveId,
                ];
                session()->set('skip_activity_logger', true);
                $logModel = new \App\Models\AdminActivityLogModel();
                $logModel->appendAction($logId, 'deactivate', '/admin/deactivateUser/' . $id, $this->request->getMethod() ?: 'POST', $id, $details);
            }
        } catch (\Throwable $_) {
            // ignore logging failures
        }
        // 3) Archive last two years of billings before inactivation
        // Prefer billing_month as reference; fallback to created_at
        $twoYearsAgo = date('Y-m-d', strtotime('-2 years', strtotime($now)));

        // Fetch eligible billings
        $billings = $db->table('billings')
            ->select('*')
            ->where('user_id', $id)
            ->groupStart()
                ->groupStart()
                    ->where('billing_month <=', date('Y-m-d', strtotime($now)))
                    ->where('billing_month >=', $twoYearsAgo)
                ->groupEnd()
                ->orGroupStart()
                    ->where('billing_month', null)
                    ->where('DATE(created_at) <=', date('Y-m-d', strtotime($now)))
                    ->where('DATE(created_at) >=', $twoYearsAgo)
                ->groupEnd()
            ->groupEnd()
            ->get()
            ->getResultArray();

        if (!empty($billings)) {
            foreach ($billings as $b) {
                $db->table('archived_billings')->insert([
                    'inactive_ref_id'    => $inactiveId,
                    'original_billing_id'=> $b['id'],
                    'user_id'            => $b['user_id'],
                    'bill_no'            => $b['bill_no'] ?? null,
                    'amount_due'         => $b['amount_due'] ?? null,
                    'billing_month'      => $b['billing_month'] ?? null,
                    'due_date'           => $b['due_date'] ?? null,
                    'status'             => $b['status'] ?? null,
                    'paid_date'          => $b['paid_date'] ?? null,
                    'created_at'         => $b['created_at'] ?? null,
                    'updated_at'         => $b['updated_at'] ?? null,
                    'archived_at'        => $now,
                ]);
            }

            // Remove archived rows from active billings
            $ids = array_column($billings, 'id');
            if (!empty($ids)) {
                $db->table('billings')->whereIn('id', $ids)->delete();
            }
        }

        $db->transComplete();
        if (!$db->transStatus()) {
            if ($this->request->isAJAX() || $this->request->is('post')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to deactivate and archive user records.']);
            }
            return redirect()->back()->with('error', 'Failed to deactivate and archive user records.');
        }

        if ($this->request->isAJAX() || $this->request->is('post')) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deactivated and last 2 years of billings archived.']);
        }
        return redirect()->back()->with('success', 'User deactivated and last 2 years of billings archived.');
    }

    public function suspendUser($id)
    {
        $this->usersModel->update($id, [
            'active' => -1,
            'status' => 'suspended'
        ]);
        return redirect()->back()->with('success', 'User suspended successfully.');
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

    // ===================== PASSWORD CHANGE (OTP) =====================
    public function requestPasswordOtp()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        // Basic cooldown: 60 seconds between OTP requests
        $now = time();
        $lastReq = (int) (session()->get('pwd_otp_last') ?? 0);
        if ($now - $lastReq < 60) {
            $wait = 60 - ($now - $lastReq);
            return $this->response->setJSON(['success' => false, 'message' => 'Please wait ' . $wait . 's before requesting a new OTP']);
        }

        $currentPassword = $this->request->getPost('current_password');
        if (empty($currentPassword)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Current password is required']);
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Admin not found']);
        }

        if (!password_verify($currentPassword, $admin['password'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Current password is incorrect']);
        }

        // Generate 6-digit OTP
        try {
            $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            $otp = (string)mt_rand(100000, 999999);
        }
        $expiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes

        // Store plain OTP to be compatible with existing column sizes
        $this->adminModel->update($adminId, [
            'otp_code' => $otp,
            'otp_expire' => $expiry,
        ]);

        
        // Send OTP using Brevo Transactional Email API
        require ROOTPATH . 'vendor/autoload.php';
        $mailerSent = false;
        try {
            $config = Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', getenv('BREVO_API_KEY'));
            $apiInstance = new TransactionalEmailsApi(new GuzzleClient(), $config);

            $fromEmail = getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
            $email = new SendSmtpEmail([
                'subject' => 'Password Change - One Time Password (OTP)',
                'sender' => ['name' => 'Password Security', 'email' => $fromEmail],
                'to' => [[ 'email' => $admin['email'] ]],
                'htmlContent' =>
                    '<h3>Password Change Request</h3>' .
                    '<p>Hello ' . esc($admin['first_name'] ?? 'Admin') . ',</p>' .
                    '<p>Your One Time Password (OTP) is:</p>' .
                    '<h2><b>' . $otp . '</b></h2>' .
                    '<p>This code will expire in 5 minutes. Do not share this code with anyone.</p>' .
                    '<br><small>This is an automated message. Please do not reply.</small>'
            ]);

            $apiInstance->sendTransacEmail($email);
            $mailerSent = true;
        } catch (\Throwable $e) {
            log_message('error', 'Brevo OTP email failed: ' . $e->getMessage());
        }

        // Reset counters on new OTP
        session()->set('pwd_otp_last', $now);
        session()->remove('pwd_otp_fail_count');
        session()->remove('pwd_otp_locked_until');

        $response = [
            'success' => true,
            'message' => $mailerSent ? 'OTP sent to your email. It will expire in 5 minutes.' : 'OTP generated. Email delivery failed; contact admin.',
            'emailSent' => $mailerSent,
        ];

        return $this->response->setJSON($response);
    }

    public function changePassword()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $otpCode = $this->request->getPost('otp_code');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (empty($otpCode) || empty($newPassword) || empty($confirmPassword)) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required']);
        }
        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['success' => false, 'message' => 'Passwords do not match']);
        }
        // Updated password policy: min 6 chars, at least one capital letter, one number, one special character
        if (strlen($newPassword) < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must be at least 6 characters']);
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must contain at least one uppercase letter']);
        }
        if (!preg_match('/\d/', $newPassword)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must contain at least one number']);
        }
        if (!preg_match('/[!@#$%^&*()_\-+=\[\]{};:"' . "'" . '<>,.?\/|`~]/', $newPassword)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password must contain at least one special character']);
        }

        // Check temporary lockout after too many failed attempts
        $lockedUntil = (int) (session()->get('pwd_otp_locked_until') ?? 0);
        if ($lockedUntil > time()) {
            $wait = $lockedUntil - time();
            return $this->response->setJSON(['success' => false, 'message' => 'Too many attempts. Try again in ' . $wait . 's']);
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin || empty($admin['otp_code']) || empty($admin['otp_expire'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'No OTP request found']);
        }
        if (strtotime($admin['otp_expire']) < time()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid or expired OTP']);
        }
        $storedOtp = (string)($admin['otp_code'] ?? '');
        $isValid = false;
        // Support both hashed and plain storage
        if (strlen($storedOtp) > 20) {
            // Looks like a hash
            $isValid = password_verify($otpCode, $storedOtp);
        } else {
            // Plain numeric OTP
            $isValid = hash_equals($storedOtp, $otpCode);
        }
        if (!$isValid) {
            $fails = (int) (session()->get('pwd_otp_fail_count') ?? 0);
            $fails++;
            session()->set('pwd_otp_fail_count', $fails);
            if ($fails >= 5) {
                // Lock for 5 minutes and clear current OTP
                session()->set('pwd_otp_locked_until', time() + 300);
                $this->adminModel->update($adminId, ['otp_code' => null, 'otp_expire' => null]);
                return $this->response->setJSON(['success' => false, 'message' => 'Too many attempts. Please request a new OTP in 5 minutes']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid or expired OTP']);
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->adminModel->update($adminId, [
            'password' => $hashedPassword,
            'otp_code' => null,
            'otp_expire' => null,
        ]);

        session()->remove('pwd_otp_fail_count');
        session()->remove('pwd_otp_locked_until');

        return $this->response->setJSON(['success' => true, 'message' => 'Password changed successfully']);
    }


    // ---------------- Logs Functions ----------------
        // Activity logs view (admin)
    public function logs()
    {
        return view('admin/logs');
    }

    // JSON: latest admin logs
    public function getLogs()
    {
        $limit = (int) ($this->request->getGet('limit') ?? 200);
        $limit = max(1, min(500, $limit));
        $model = new \App\Models\AdminActivityLogModel();
        $builder = $model->where('actor_type', 'admin');

        $action = trim($this->request->getGet('action') ?? '');
        $method = trim($this->request->getGet('method') ?? '');
        $actorId = (int) ($this->request->getGet('actor_id') ?? 0);
        $logId = (int) ($this->request->getGet('log_id') ?? 0);
        $q = trim($this->request->getGet('q') ?? '');
        $start = trim($this->request->getGet('start') ?? '');
        $end = trim($this->request->getGet('end') ?? '');

        if ($actorId > 0) { $builder->where('actor_id', $actorId); }
        if ($logId > 0) { $builder->where('id', $logId); }
        if ($action !== '') { $builder->where('action', $action); }
        if ($method !== '') { $builder->where('method', strtoupper($method)); }
        if ($start !== '') { $builder->where('created_at >=', $start . ' 00:00:00'); }
        if ($end !== '') { $builder->where('created_at <=', $end . ' 23:59:59'); }
        if ($q !== '') {
            // Search admin first/last name instead of route/resource/details
            try {
                $admModel = new \App\Models\AdminModel();
                $matches = $admModel->groupStart()
                    ->like('first_name', $q)
                    ->orLike('last_name', $q)
                    ->orLike("CONCAT(first_name, ' ', last_name)", $q)
                ->groupEnd()
                ->get()
                ->getResultArray();
                $ids = array_column($matches, 'id');
                if (!empty($ids)) {
                    $builder->whereIn('actor_id', $ids);
                } else {
                    // no matching admin names, ensure empty result
                    $builder->where('actor_id', 0);
                }
            } catch (\Throwable $_) {
                // fallback: return no results on error
                $builder->where('actor_id', 0);
            }
        }
        $rows = $builder->orderBy('id','DESC')->findAll($limit);
        // Enrich rows with actor display name for admins to allow client to show friendly names
        try {
            foreach ($rows as &$rr) {
                if (($rr['actor_type'] ?? '') === 'admin' && !empty($rr['actor_id'])) {
                    $adm = $this->adminModel->find((int)$rr['actor_id']);
                    if ($adm) {
                        $rr['actor_name'] = trim(($adm['first_name'] ?? '') . ' ' . ($adm['last_name'] ?? ''));
                    }
                }
            }
            unset($rr);
        } catch (\Throwable $_) {
            // ignore enrichment errors
        }
        return $this->response->setJSON($rows);
    }

    // CSV export for admin logs (read-only)
    public function exportLogs()
    {
        $format = strtolower($this->request->getGet('format') ?? 'csv');
        $fileBase = 'admin-activity-logs-' . date('Y-m-d');

        $model = new \App\Models\AdminActivityLogModel();
        $builder = $model->where('actor_type', 'admin');
        $action = trim($this->request->getGet('action') ?? '');
        $method = trim($this->request->getGet('method') ?? '');
        $actorId = (int) ($this->request->getGet('actor_id') ?? 0);
        $logId = (int) ($this->request->getGet('log_id') ?? 0);
        $q = trim($this->request->getGet('q') ?? '');
        $start = trim($this->request->getGet('start') ?? '');
        $end = trim($this->request->getGet('end') ?? '');
        if ($actorId > 0) { $builder->where('actor_id', $actorId); }
        if ($logId > 0) { $builder->where('id', $logId); }
        if ($action !== '') { $builder->where('action', $action); }
        if ($method !== '') { $builder->where('method', strtoupper($method)); }
        if ($start !== '') { $builder->where('created_at >=', $start . ' 00:00:00'); }
        if ($end !== '') { $builder->where('created_at <=', $end . ' 23:59:59'); }
        if ($q !== '') {
            // Search admin first/last name instead of route/resource/details for exports
            try {
                $admModel = new \App\Models\AdminModel();
                $matches = $admModel->groupStart()
                    ->like('first_name', $q)
                    ->orLike('last_name', $q)
                    ->orLike("CONCAT(first_name, ' ', last_name)", $q)
                ->groupEnd()
                ->get()
                ->getResultArray();
                $ids = array_column($matches, 'id');
                if (!empty($ids)) {
                    $builder->whereIn('actor_id', $ids);
                } else {
                    $builder->where('actor_id', 0);
                }
            } catch (\Throwable $_) {
                $builder->where('actor_id', 0);
            }
        }

        $rows = $builder->orderBy('id','DESC')->findAll(2000);
        $fh = fopen('php://temp', 'w+');
        // Determine requester type: only include raw JSON for superadmins
        $session = session();
        $isSuper = (bool) $session->get('is_superadmin_logged_in');

        $headers = ['Time','Actor','Action','Method','Route','Resource','Performed By','Details Summary'];
        if ($isSuper) $headers[] = 'Details (raw JSON)';
        $headers = array_merge($headers, ['IP','User Agent','Logged Out']);
        fputcsv($fh, $headers);
        // For each log row, flatten inner actions into individual CSV rows
        $superModel = null;
        foreach ($rows as $r) {
            // Determine performer display name from actor tables
            $actorKey = ($r['actor_type'] ?? '') . '#' . ($r['actor_id'] ?? '');
            $performedBy = $actorKey;
            try {
                if (($r['actor_type'] ?? '') === 'admin') {
                    $adm = $this->adminModel->find((int)($r['actor_id'] ?? 0));
                    if ($adm) $performedBy = trim(($adm['first_name'] ?? '') . ' ' . ($adm['last_name'] ?? '')) ?: $actorKey;
                } elseif (($r['actor_type'] ?? '') === 'superadmin') {
                    if ($superModel === null) $superModel = new \App\Models\SuperAdminModel();
                    $sa = $superModel->find((int)($r['actor_id'] ?? 0));
                    if ($sa) $performedBy = $sa['email'] ?? $actorKey;
                }
            } catch (\Throwable $_) {
                // ignore and fallback to actorKey
            }

            if (!empty($r['details'])) {
                $d = json_decode($r['details'], true);
                // Session-merged details array
                if (is_array($d) && isset($d[0]) && is_array($d[0]) && isset($d[0]['action'])) {
                    foreach ($d as $act) {
                        $time = $act['time'] ?? $r['created_at'] ?? '';
                        // Prefix time with apostrophe to force CSV/Excel to treat as text (prevents #### display)
                        if ($time !== '') $time = "'" . $time;
                        $actName = $act['action'] ?? ($r['action'] ?? '');
                        $method = $act['method'] ?? $r['method'] ?? '';
                        $route = $act['route'] ?? $r['route'] ?? '';
                        $resource = $act['resource'] ?? $r['resource'] ?? '';

                        // Build details summary for this inner action
                        $summaryParts = [];
                        if (!empty($act['details'])) {
                            $det = json_decode($act['details'], true);
                            if (is_array($det)) {
                                if (!empty($det['user_name'])) $summaryParts[] = 'User: ' . $det['user_name'];
                                elseif (!empty($det['first_name']) || !empty($det['last_name'])) $summaryParts[] = 'User: ' . trim(($det['first_name'] ?? '') . ' ' . ($det['last_name'] ?? ''));
                                elseif (!empty($det['id'])) $summaryParts[] = 'User ID: ' . $det['id'];
                                if (!empty($det['reason'])) $summaryParts[] = 'Reason: ' . $det['reason'];
                            }
                        }
                        if ($resource) $summaryParts[] = 'Resource: ' . $resource;
                        $detailsSummary = implode('; ', $summaryParts);

                        $row = [
                            $time,
                            $actorKey,
                            $actName,
                            $method,
                            $route,
                            $resource,
                            $performedBy,
                            $detailsSummary,
                        ];
                        if ($isSuper) $row[] = ($act['details'] ?? $r['details'] ?? '');
                        $row = array_merge($row, [
                            $r['ip_address'] ?? '',
                            $r['user_agent'] ?? '',
                            $r['logged_out_at'] ?? '',
                        ]);
                        fputcsv($fh, $row);
                    }
                    continue;
                }
                // Single-action details (not an array)
                $time = $r['created_at'] ?? '';
                if ($time !== '') $time = "'" . $time;
                $actName = $r['action'] ?? '';
                $method = $r['method'] ?? '';
                $route = $r['route'] ?? '';
                $resource = $r['resource'] ?? '';

                $summaryParts = [];
                $det = is_array($d) ? $d : [];
                if (!empty($det['user_name'])) $summaryParts[] = 'User: ' . $det['user_name'];
                elseif (!empty($det['first_name']) || !empty($det['last_name'])) $summaryParts[] = 'User: ' . trim(($det['first_name'] ?? '') . ' ' . ($det['last_name'] ?? ''));
                elseif (!empty($det['id'])) $summaryParts[] = 'User ID: ' . ($det['id'] ?? '');
                if (!empty($det['reason'])) $summaryParts[] = 'Reason: ' . $det['reason'];
                if ($resource) $summaryParts[] = 'Resource: ' . $resource;
                $detailsSummary = implode('; ', $summaryParts);

                $row = [
                    $time,
                    $actorKey,
                    $actName,
                    $method,
                    $route,
                    $resource,
                    $performedBy,
                    $detailsSummary,
                ];
                if ($isSuper) $row[] = ($r['details'] ?? '');
                $row = array_merge($row, [
                    $r['ip_address'] ?? '',
                    $r['user_agent'] ?? '',
                    $r['logged_out_at'] ?? '',
                ]);
                fputcsv($fh, $row);
                continue;
            }

            // If no details present, output a minimal row for the log
            $row = [
                ($r['created_at'] ? "'" . $r['created_at'] : ''),
                $actorKey,
                $r['action'] ?? '',
                $r['method'] ?? '',
                $r['route'] ?? '',
                $r['resource'] ?? '',
                $performedBy,
                '',
            ];
            if ($isSuper) $row[] = ($r['details'] ?? '');
            $row = array_merge($row, [
                $r['ip_address'] ?? '',
                $r['user_agent'] ?? '',
                $r['logged_out_at'] ?? '',
            ]);
            fputcsv($fh, $row);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        // If XLSX requested and PhpSpreadsheet is available, produce a workbook
        if (($format === 'excel' || $format === 'xlsx') && class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            try {
                $lines = array_values(array_filter(array_map('trim', explode("\n", trim($csv)))));
                if (empty($lines)) {
                    // Nothing to export, return empty CSV as fallback
                    return $this->response
                        ->setHeader('Content-Type', 'text/csv')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileBase . '.csv"')
                        ->setBody('');
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Logs');

                // Build rows array from CSV lines
                $rowsArr = [];
                foreach ($lines as $ln) {
                    $rowsArr[] = str_getcsv($ln);
                }

                // Populate sheet with rows
                $sheet->fromArray($rowsArr, null, 'A1');

                // Compute simple action counts (Action column)
                $headerRow = $rowsArr[0];
                $actionIdx = array_search('Action', $headerRow);
                $actionCounts = [];
                if ($actionIdx !== false) {
                    for ($i = 1; $i < count($rowsArr); $i++) {
                        $val = $rowsArr[$i][$actionIdx] ?? '';
                        $val = trim($val);
                        if ($val === '') continue;
                        if (!isset($actionCounts[$val])) $actionCounts[$val] = 0;
                        $actionCounts[$val]++;
                    }
                }

                // If we have action counts, add a Stats sheet and a simple bar chart
                if (!empty($actionCounts)) {
                    $stats = $spreadsheet->createSheet();
                    $stats->setTitle('Stats');
                    $stats->setCellValue('A1', 'Action');
                    $stats->setCellValue('B1', 'Count');
                    $r = 2;
                    foreach ($actionCounts as $act => $cnt) {
                        $stats->setCellValue('A' . $r, $act);
                        $stats->setCellValue('B' . $r, $cnt);
                        $r++;
                    }

                    $lastRow = $r - 1;
                    // Build chart series references
                    $catRef = "'" . $stats->getTitle() . "'!\$A\$2:\$A\$" . $lastRow;
                    $valRef = "'" . $stats->getTitle() . "'!\$B\$2:\$B\$" . $lastRow;

                    $categories = new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', $catRef, null, ($lastRow - 1));
                    $values = new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', $valRef, null, ($lastRow - 1));

                    $series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
                        \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,
                        null,
                        [0],
                        [],
                        [$categories],
                        [$values]
                    );
                    $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$series]);
                    $title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Actions Count');
                    $legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_RIGHT, null, false);
                    $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart('action_chart', $title, $legend, $plotArea, true, 0, null, null);
                    $chart->setTopLeftPosition('D2');
                    $chart->setBottomRightPosition('L20');
                    $stats->addChart($chart);
                }

                // Output XLSX
                $filename = $fileBase . '.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->setIncludeCharts(true);
                $writer->save('php://output');
                exit;
            } catch (\Throwable $e) {
                log_message('error', 'XLSX export failed: ' . $e->getMessage());
                // fall back to CSV below
            }
        }

        // Prefer PDF output, but fall back to CSV if Dompdf isn't available.
        if (! class_exists('\\Dompdf\\Dompdf')) {
            log_message('warning', 'Dompdf not installed; falling back to CSV export.');
            // Return CSV so the frontend download flow continues to work.
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileBase . '.csv"')
                ->setBody($csv);
        }

        // Build filter labels for the PDF header
        $filterParts = [];
        if ($start || $end) $filterParts[] = 'Date: ' . ($start ?: '...') . ' to ' . ($end ?: '...');
        if ($action) $filterParts[] = 'Action: ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8');
        if ($method) $filterParts[] = 'Method: ' . htmlspecialchars($method, ENT_QUOTES, 'UTF-8');
        if ($q) $filterParts[] = 'Search: ' . htmlspecialchars($q, ENT_QUOTES, 'UTF-8');

        $metaHtml = '';
        if (! empty($filterParts)) {
            $metaHtml .= '<div style="margin-bottom:10px;">';
            foreach ($filterParts as $p) {
                $metaHtml .= '<span style="display:inline-block;background:#eef2ff;padding:6px 8px;border-radius:6px;margin-right:8px;font-size:11px;color:#0b2e6f">' . $p . '</span>';
            }
            $metaHtml .= '</div>';
        }

        // Generate HTML table for PDF
        $html = '<!doctype html><html><head><meta charset="utf-8"><style>'
            . 'body{font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#111; font-size:12px;}'
            . 'h1{font-size:16px;margin:0 0 6px 0}'
            . '.meta{font-size:11px;color:#555;margin-bottom:8px}'
            . 'table{border-collapse:collapse;width:100%;margin-top:6px}'
            . 'th,td{border:1px solid #ddd;padding:6px;text-align:left;vertical-align:top;font-size:11px}'
            . 'th{background:#f7f7f7;font-weight:700}'
            . '</style></head><body>';

        $html .= '<h1>Admin Activity Logs</h1>';
        $html .= '<div class="meta">Generated: ' . date('Y-m-d H:i:s') . '</div>';
        $html .= $metaHtml;

        $html .= '<table><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        // Parse CSV rows into table (skip CSV header)
        $lines = explode("\n", trim($csv));
        foreach ($lines as $idx => $line) {
            if ($line === '') continue;
            if ($idx === 0) continue; // header
            $cells = str_getcsv($line);
            $html .= '<tr>';
            foreach ($cells as $cell) {
                $html .= '<td>' . htmlspecialchars(trim($cell), ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        try {
            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->loadHtml($html);
            $dompdf->render();
            $pdf = $dompdf->output();
            return $this->response->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="admin-activity-logs.pdf"')
                ->setBody($pdf);
        } catch (\Throwable $e) {
            log_message('error', 'PDF export failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Failed to generate PDF export.');
        }
    }

    /**
     * Return available export formats based on installed libraries.
     * JSON: { pdf: bool, xlsx: bool }
     */
    public function exportAvailability()
    {
        $available = [
            'pdf'  => class_exists('\\Dompdf\\Dompdf'),
            'xlsx' => class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet'),
        ];
        return $this->response->setJSON($available);
    }
}