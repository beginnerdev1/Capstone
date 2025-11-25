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
                             SUM(CASE WHEN MONTH(billings.updated_at) = {$currentMonth} THEN amount_due ELSE 0 END) as monthly_total")
                    ->whereIn('status', ['Paid', 'Over the Counter'])
                    ->where('YEAR(billings.updated_at)', $currentYear)
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
                        DATE_FORMAT(billings.updated_at, '%b') AS month,
                        MONTH(billings.updated_at) AS month_num,
                        SUM(amount_due) AS total
                    ")
                    ->whereIn('status', ['Paid', 'Over the Counter'])
                    ->where('YEAR(billings.updated_at)', $currentYear)
                    ->groupBy('MONTH(billings.updated_at)')
                    ->orderBy('MONTH(billings.updated_at)', 'ASC')
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
                                 SUM(CASE WHEN MONTH(billings.updated_at) = {$currentMonth} THEN amount_due ELSE 0 END) as monthly_total")
                            ->where('billings.status', 'Paid')
                        ->where('YEAR(billings.updated_at)', $currentYear)
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
                        MONTH(billings.updated_at) AS month_num,
                        SUM(amount_due) AS total
                    ")
                        ->where('billings.status', 'Paid')
                    ->where('YEAR(billings.updated_at)', $currentYear)
                    ->groupBy('MONTH(billings.updated_at)')
                    ->orderBy('MONTH(billings.updated_at)', 'ASC')
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
            $lineNumber    = trim((string)$this->request->getPost('line_number'));
            $status        = strtolower(trim((string)$this->request->getPost('status') ?? 'approved'));

            $errors = [];
            if (strlen($firstName) < 2) $errors['first_name'] = 'First name must be at least 2 characters';
            if (strlen($lastName) < 2)  $errors['last_name']  = 'Last name must be at least 2 characters';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
            helper('email');
            if (!is_real_email($email)) $errors['email'] = 'Please provide a valid, deliverable email address';
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
                'line_number'   => $lineNumber,
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

            // Send credentials email to the new user (best-effort, non-blocking)
            $emailSent = false;
            $emailDriver = null;
            $emailDebug = null;
            try {
                $apiKey = getenv('BREVO_API_KEY') ?: null;
                $siteUrl = rtrim(base_url(), '/');
                $loginUrl = $siteUrl . '/login';
                $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                $fromName  = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';

                $displayName = trim($firstName . ' ' . $lastName) ?: $email;
                $displayNameEsc = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                $loginUrlEsc = htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8');
                $fromNameEsc = htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8');

                $subject = 'Your account has been created';
                $pwdEsc = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
                $emailEsc = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

                $html = "<!doctype html><html><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">";
                $html .= '<style>body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fa;margin:0;padding:20px} .card{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,0.06)}.header{background:linear-gradient(135deg,#2563eb 0%,#1e40af 100%);color:#fff;padding:18px}.body{padding:20px;color:#111827}.btn{display:inline-block;padding:10px 16px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none;font-weight:600}.meta{font-size:13px;color:#6b7280;margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9}</style>';
                $html .= '<body><div class="card"><div class="header"><h3 style="margin:0;font-size:18px">Welcome to ' . htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'Our Service', ENT_QUOTES, 'UTF-8') . '</h3></div><div class="body">';
                $html .= '<p style="margin:0 0 12px;">Hello ' . $displayNameEsc . ',</p>';
                $html .= '<p style="margin:0 0 12px;line-height:1.5;">An account has been created for you. Use the credentials below to sign in. Please change your password after first login.</p>';
                $html .= '<p style="margin:0 0 8px;"><strong>Email:</strong> ' . $emailEsc . '</p>';
                $html .= '<p style="margin:0 0 8px;"><strong>Password:</strong> <code style="background:#f3f4f6;padding:4px 6px;border-radius:4px">' . $pwdEsc . '</code></p>';
                $ctaStyle = 'display:inline-block;padding:10px 16px;border-radius:6px;background:#2563eb;color:#ffffff!important;text-decoration:none;font-weight:600';
                $html .= '<p style="margin:14px 0;"><a href="' . $loginUrlEsc . '" style="' . $ctaStyle . '">Sign in to your account</a></p>';
                $html .= '<p class="meta">If you did not request this account, please contact support. This message includes the email and the password you can use to sign in.</p>';
                $html .= '</div><div style="padding:12px 20px;background:#fbfdff;color:#6b7280;font-size:12px">' . $fromNameEsc . ' — Automated notification</div></div></body></html>';

                $plain = "Hello {$displayName}\n\nAn account has been created for you.\nEmail: {$email}\nPassword: {$password}\n\nSign in at: {$loginUrl}\n\nPlease change your password after first login.";

                if ($apiKey) {
                    $emailDriver = 'brevo';
                    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

                    $mailObj = new \Brevo\Client\Model\SendSmtpEmail([
                        'subject' => $subject,
                        'sender' => ['name' => $fromName, 'email' => $fromEmail],
                        'to' => [[ 'email' => $email, 'name' => $displayName ]],
                        'htmlContent' => $html,
                        'textContent' => $plain,
                    ]);

                    try {
                        $resp = $apiInstance->sendTransacEmail($mailObj);
                        $emailSent = true;
                        log_message('info', 'addUser: send email invoked for ' . $email . ' via Brevo');
                        // Optionally capture response id/info
                        if (is_object($resp)) {
                            $emailDebug = json_encode($resp);
                        }
                    } catch (\Throwable $ie) {
                        $emailSent = false;
                        log_message('error', 'addUser: send email failed to ' . $email . ' via Brevo: ' . $ie->getMessage());
                        $emailDebug = $ie->getMessage();
                    }
                } else {
                    $emailDriver = 'ci';
                    try {
                        $emailService = \Config\Services::email();
                        $emailService->setFrom($fromEmail, $fromName);
                        $emailService->setTo($email);
                        $emailService->setSubject($subject);
                        if (method_exists($emailService, 'setMailType')) {
                            $emailService->setMailType('html');
                        }
                        $emailService->setMessage($html);
                        if (method_exists($emailService, 'setAltMessage')) {
                            $emailService->setAltMessage($plain);
                        }

                        if ($emailService->send()) {
                            $emailSent = true;
                            log_message('info', 'addUser: fallback email sent via CI Email to ' . $email);
                        } else {
                            $emailSent = false;
                            $debug = method_exists($emailService, 'printDebugger') ? $emailService->printDebugger(['headers']) : '';
                            log_message('error', 'addUser: fallback email failed to ' . $email . ' debug: ' . $debug);
                            $emailDebug = $debug;
                        }
                    } catch (\Throwable $e) {
                        $emailSent = false;
                        log_message('error', 'addUser: fallback email exception for ' . $email . ': ' . $e->getMessage());
                        $emailDebug = $e->getMessage();
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'addUser: email send wrapper failed: ' . $e->getMessage());
                $emailSent = false;
                $emailDebug = $e->getMessage();
            }
            

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User added successfully.',
                'id' => (int)$userId,
                'email_sent' => (bool)$emailSent,
                'email_driver' => $emailDriver,
                'email_debug' => $emailDebug,
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

    // Suspended Users page (view)
    public function suspendedUsers()
    {
        return view('admin/suspended_users');
    }

    // Fetch suspended users (AJAX)
    public function getSuspendedUsers()
    {
        $search = trim((string)$this->request->getGet('search'));
        $role   = trim((string)$this->request->getGet('role'));
        $from   = trim((string)$this->request->getGet('from'));
        $to     = trim((string)$this->request->getGet('to'));

        $db = \Config\Database::connect();
        $builder = $db->table('users u')
            ->select('u.id, u.email, u.active, u.status, u.updated_at, ui.first_name, ui.last_name, ui.phone, ui.purok')
            ->join('user_information ui', 'ui.user_id = u.id', 'left')
            ->groupStart()
                ->where('u.status', 'suspended')
                ->orWhere('u.active', -1)
            ->groupEnd();

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.email', $search)
                ->orLike('ui.first_name', $search)
                ->orLike('ui.last_name', $search)
                ->groupEnd();
        }
        // Note: user role is not stored in schema; ignore `role` filter if provided.
        if ($from !== '' && $to !== '') {
            $builder->where('DATE(u.updated_at) >=', $from)
                    ->where('DATE(u.updated_at) <=', $to);
        }

        // Pagination
        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $limit = max(10, (int)($this->request->getGet('limit') ?? 20));
        $offset = ($page - 1) * $limit;

        $total = (int)$builder->countAllResults(false);
        $rows = $builder->orderBy('u.updated_at', 'DESC')->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'data' => $rows,
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $limit]
        ]);
    }

    // Overdue bills feature removed: the related view and AJAX endpoints were intentionally removed
    // to simplify the dashboard. Overdue payments remain available via the existing
    // `overduePayments` endpoints and views.

    // Mark a bill as paid (AJAX)
    public function markBillPaid($id)
    {
        try {
            $ok = $this->billingModel->updateBillingStatus((int)$id, 'Paid');
            if ($ok) {
                return $this->response->setJSON(['success' => true]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update billing']);
        } catch (\Throwable $e) {
            log_message('error', 'markBillPaid error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error']);
        }
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
        try {
            $search = $this->request->getVar('search');
            $purok = $this->request->getVar('purok');
            $status = $this->request->getVar('status');
        // Build base query including pending bill counts per user (aggregate join)
        $builder = $this->usersModel
            ->select('users.id, users.email, users.status, user_information.first_name, user_information.last_name, user_information.purok, user_information.line_number, SUM(CASE WHEN billings.status = "Pending" THEN 1 ELSE 0 END) as pending_bills')
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

        $lineNumber = $this->request->getVar('line_number');
        if ($lineNumber) {
            $builder->like('user_information.line_number', $lineNumber);
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
                'line_number' => $user['line_number'] ?? null,
                'email' => $user['email'],
                'status' => $statusMap[$user['status']] ?? 'Unknown',
                'pending_bills' => (int)($user['pending_bills'] ?? 0),
            ];
        }

            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            log_message('error', 'filterUsers error: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }

        // Activate user account
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

    // Deactivate user account
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
                ->where('billings.status', 'Pending')
                ->countAllResults();
            log_message('debug', '[Admin::deactivateUser] outstanding count for user ' . $id . ' => ' . $outstanding);
            if ($outstanding > 0) {
                try {
                    $samples = $this->billingModel->select('id, bill_no, status')->where('user_id', $id)->where('billings.status', 'Pending')->findAll(5);
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

        // Send notification email to user about deactivation (non-blocking) with CI fallback
        $email_sent = false;
        $email_driver = null;
        $email_debug = null;
        try {
            // Build display name
            $fullName = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? ''));
            $displayName = $fullName ?: ($user['email'] ?? 'User');

            $siteUrl = rtrim(base_url(), '/');
            $helpUrl = $siteUrl . '/contact';
            $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
            $fromName  = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';
            $displayNameEsc = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
            $helpUrlEsc = htmlspecialchars($helpUrl, ENT_QUOTES, 'UTF-8');
            $fromNameEsc = htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8');

            $reason = $this->request->getPost('reason') ?: 'No reason provided';
            $reasonEsc = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

            $subject = 'Account Deactivated';

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
                        <h2 style="margin:0 0 8px;font-weight:600;color:#111827;font-size:20px;">Account Deactivated</h2>
                        <p style="margin:0;color:#6b7280;">Hello {$displayNameEsc},</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 24px;color:#374151;">
                        <p style="margin:0 0 12px;line-height:1.5;">Your account has been deactivated on {$now}. Reason: {$reasonEsc}.</p>
                        <p style="margin:0 0 12px;line-height:1.5;">If you believe this is a mistake or need assistance, please contact our support team.</p>
                        <p style="margin:0 0 20px;">
                            <a href="{$helpUrlEsc}" style="display:inline-block;padding:10px 18px;background:#2563eb;color:#ffffff;border-radius:6px;text-decoration:none;font-weight:600;">Contact Support</a>
                        </p>
                        <p style="margin:0;color:#9ca3af;font-size:12px;">If the button above does not work, copy and paste this link into your browser: {$helpUrlEsc}</p>
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

            $plain = "Hello {$displayName},\n\nYour account has been deactivated on {$now}. Reason: {$reason}.\n\nIf you believe this is a mistake, please contact support: {$helpUrl}\n\nThis is an automated message.";

            // Try Brevo first
            $apiKey = getenv('BREVO_API_KEY') ?: null;
            if ($apiKey) {
                try {
                    $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);
                    $email = new \Brevo\Client\Model\SendSmtpEmail([
                        'subject' => $subject,
                        'sender' => ['name' => $fromName, 'email' => $fromEmail],
                        'to' => [['email' => $user['email'], 'name' => $displayName]],
                        'htmlContent' => $html,
                        'textContent' => $plain
                    ]);
                    $apiInstance->sendTransacEmail($email);
                    $email_sent = true;
                    $email_driver = 'brevo';
                } catch (\Throwable $e) {
                    $email_debug = 'brevo: ' . $e->getMessage();
                    log_message('error', 'Deactivation email (brevo) failed for user ' . ($user['email'] ?? 'unknown') . ': ' . $e->getMessage());
                }
            } else {
                log_message('warning', 'BREVO_API_KEY not configured — will try CI Email for deactivation for user ' . ($user['email'] ?? 'unknown'));
            }

            // If Brevo not used or failed, attempt CI Email fallback
            if (!$email_sent) {
                try {
                    $emailSvc = \Config\Services::email();
                    $emailSvc->setFrom($fromEmail, $fromName);
                    $emailSvc->setTo($user['email']);
                    $emailSvc->setSubject($subject);
                    $emailSvc->setMessage($html);
                    if ($emailSvc->send()) {
                        $email_sent = true;
                        $email_driver = 'ci';
                        $email_debug = '';
                    } else {
                        $email_debug = $emailSvc->printDebugger(['headers']);
                        log_message('error', 'Deactivation email (ci) failed for user ' . ($user['email'] ?? 'unknown') . ': ' . $email_debug);
                    }
                } catch (\Throwable $e) {
                    $email_debug = 'ci: ' . $e->getMessage();
                    log_message('error', 'Deactivation email (ci) exception for user ' . ($user['email'] ?? 'unknown') . ': ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            $email_debug = 'general: ' . $e->getMessage();
            log_message('error', 'Deactivation email failed for user ' . ($user['email'] ?? 'unknown') . ': ' . $e->getMessage());
        }

        if ($this->request->isAJAX() || $this->request->is('post')) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deactivated and last 2 years of billings archived.',
                'email_sent' => $email_sent,
                'email_driver' => $email_driver,
                'email_debug' => $email_debug,
            ]);
        }
        // For non-AJAX flow keep original redirect; log email status for admins
        if (!$email_sent) {
            log_message('warning', 'Deactivation email was not sent to user ' . ($user['email'] ?? 'unknown') . '. Driver: ' . ($email_driver ?? 'none') . '. Debug: ' . ($email_debug ?? ''));
        }
        return redirect()->back()->with('success', 'User deactivated and last 2 years of billings archived.');
    }

    // Suspend user account
    public function suspendUser($id)
    {
        $this->usersModel->update($id, [
            'active' => -1,
            'status' => 'suspended'
        ]);
        return redirect()->back()->with('success', 'User suspended successfully.');
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

        $pic = $info['profile_picture'] ?? '';
        $picUrl = '';
        if (!empty($pic)) {
            // normalize stored filename (no leading slash)
            $pic = ltrim($pic, '/');
            $picUrl = base_url('uploads/profile_pictures/' . $pic);
        }

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
            'profile_picture' => $picUrl
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

            if ($this->request->isAJAX()) {
                return $this->response->setHeader('X-CSRF-Token', csrf_hash())->setJSON(['success' => true, 'message' => 'User approved successfully.']);
            }

            return redirect()->back()->with('success', 'User approved successfully.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setHeader('X-CSRF-Token', csrf_hash())->setJSON(['success' => false, 'message' => 'Failed to approve user.']);
        }

        return redirect()->back()->with('error', 'Failed to approve user.');
    }
        // Reject user account
    public function reject($id)
    {
        if (!$id) return redirect()->back()->with('error', 'No user ID provided.');

        $user = $this->usersModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Update status to rejected
        if ($this->usersModel->update($id, ['status' => 'rejected'])) {
            // Build display name
            try {
                $info = $this->userInfoModel->getByUserId($id) ?? [];
            } catch (\Throwable $_) {
                $info = [];
            }
            $fullName = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? ''));
            $displayName = $fullName ?: ($user['email'] ?? 'User');

            // Prepare email values (escaped)
            $siteUrl = rtrim(base_url(), '/');
            $helpUrl = $siteUrl . '/contact';
            $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
            $fromName  = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';
            $displayNameEsc = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
            $helpUrlEsc = htmlspecialchars($helpUrl, ENT_QUOTES, 'UTF-8');
            $fromNameEsc = htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8');

            // Send rejection email (non-blocking)
            try {
                $apiKey = getenv('BREVO_API_KEY') ?: null;
                if ($apiKey) {
                    $config = \Brevo\Client\Configuration::getDefaultConfiguration()
                        ->setApiKey('api-key', $apiKey);
                    $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

                    $subject = 'Your account application status';

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
                                <h2 style="margin:0 0 8px;font-weight:600;color:#111827;font-size:20px;">Application Update</h2>
                                <p style="margin:0;color:#6b7280;">Hello {$displayNameEsc},</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 32px 24px;color:#374151;">
                                <p style="margin:0 0 12px;line-height:1.5;">We regret to inform you that your account application has been rejected by our administrator. If you believe this is an error or need more details, please contact our support team.</p>
                                <p style="margin:0 0 20px;">
                                    <a href="{$helpUrlEsc}" style="display:inline-block;padding:10px 18px;background:#2563eb;color:#ffffff;border-radius:6px;text-decoration:none;font-weight:600;">Contact Support</a>
                                </p>
                                <p style="margin:0;color:#9ca3af;font-size:12px;">If the button above does not work, copy and paste this link into your browser: {$helpUrlEsc}</p>
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

                    $plain = "Hello {$displayName},\n\nWe regret to inform you that your account application has been rejected. If you believe this is an error, please contact support: {$helpUrl}\n\nThis is an automated message.";

                    $email = new \Brevo\Client\Model\SendSmtpEmail([
                        'subject' => $subject,
                        'sender' => ['name' => $fromName, 'email' => $fromEmail],
                        'to' => [['email' => $user['email'], 'name' => $displayName]],
                        'htmlContent' => $html,
                        'textContent' => $plain
                    ]);

                    $apiInstance->sendTransacEmail($email);
                } else {
                    log_message('warning', 'BREVO_API_KEY not configured — skipping rejection email for user ' . ($user['email'] ?? 'unknown'));
                }
            } catch (\Throwable $e) {
                log_message('error', 'Rejection email failed for user ' . ($user['email'] ?? 'unknown') . ': ' . $e->getMessage());
            }

            if ($this->request->isAJAX()) {
                return $this->response->setHeader('X-CSRF-Token', csrf_hash())->setJSON(['success' => true, 'message' => 'User rejected successfully.']);
            }

            return redirect()->back()->with('success', 'User rejected successfully.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setHeader('X-CSRF-Token', csrf_hash())->setJSON(['success' => false, 'message' => 'Failed to reject user.']);
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

            // Start transaction for DB and file operation safety
            $db = \Config\Database::connect();
            $db->transStart();

            // Ensure we operate on a single settings row — use the first if present
            $settings = $model->orderBy('id', 'ASC')->first();
            $file = $this->request->getFile('gcash_qr');
            $qrCodePath = null;

            // If a new file was uploaded, move it to uploads and prepare to delete the old file
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/qrcodes';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $qrCodePath = 'uploads/qrcodes/' . $newName;

                // If there is an existing settings record and it has an old qr path, delete the old file
                if ($settings && !empty($settings['qr_code_path'])) {
                    $oldPath = FCPATH . $settings['qr_code_path'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }

            $gcashNumber = $this->request->getPost('gcash_number');

            // Build data to update. Always overwrite the gcash_number.
            $updateData = ['gcash_number' => $gcashNumber];
            if ($qrCodePath !== null) {
                $updateData['qr_code_path'] = $qrCodePath;
            }

            if ($settings) {
                // Update existing settings row
                $model->update($settings['id'], $updateData);

                // Remove any stray extra rows to keep a single settings record (defensive)
                $model->where('id !=', $settings['id'])->delete();
            } else {
                // No existing settings: insert a new single-row record
                $model->insert($updateData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database transaction failed while saving settings']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Settings saved successfully']);
        } catch (\Exception $e) {
            // Attempt to log and return a safe error
            log_message('error', 'saveGcashSettings error: ' . $e->getMessage());
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

            $reference = '-';
            if ($methodValue === 'gateway' && !empty($payment['payment_intent_id'])) {
                $reference = $payment['payment_intent_id'];
            } elseif (!empty($payment['reference_number'])) {
                $reference = $payment['reference_number'];
            }

            return [
                'id' => $payment['id'],
                'user_name' => $payment['user_name'] ?? 'Unknown',
                'email' => $payment['email'] ?? 'N/A',
                'amount' => $payment['amount'] ?? 0,
                'method' => $methodValue,
                'status' => strtolower($payment['status'] ?? 'pending'),
                'reference_number' => $reference, // <-- now uses computed reference
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

    try {
        // Confirm payment
        $updated = $this->paymentsModel->confirmGCashPayment($paymentId, $adminRef);
        $billingUpdated = false;
        $billingId = null;

        if ($updated) {
            $payment = $this->paymentsModel->find($paymentId);
            $userId = $payment['user_id'] ?? null;
            $toEmail = $payment['email'] ?? null;
            $billingId = $payment['billing_id'] ?? null;

            if (!$toEmail && $userId) {
                $u = $this->usersModel->find($userId);
                $toEmail = $u['email'] ?? null;
            }

            $paymentStatus = 'Paid';

            if ($billingId) {
                try {
                    $bill = $this->billingModel->find($billingId);

                    if ($bill) {
                        $amountPaid = floatval($payment['amount']);
                        $carryover = floatval($bill['carryover'] ?? 0);
                        $balance = floatval($bill['balance']);
                        $amountDue = floatval($bill['amount_due']);

                        if ($carryover > 0) {
                            $deductFromCarry = min($amountPaid, $carryover);
                            $carryover -= $deductFromCarry;
                            $amountPaid -= $deductFromCarry;
                        }

                        if ($amountPaid > 0 && $balance > 0) {
                            $deductFromBalance = min($amountPaid, $balance);
                            $balance -= $deductFromBalance;
                            $amountPaid -= $deductFromBalance;
                        }

                        $paymentStatus = ($balance <= 0 && $carryover <= 0) ? 'Paid' : 'Partial';

                        $billingUpdated = $this->billingModel->update($billingId, [
                            'carryover' => $carryover,
                            'balance' => $balance,
                            'status' => $paymentStatus
                        ]);

                        // Update payment status in payments table
                        $this->paymentsModel->update($paymentId, ['status' => strtolower($paymentStatus), 'paid_at' => date('Y-m-d H:i:s')]);

                        log_message('info', "Billing updated. ID {$billingId}, Status {$paymentStatus}, Carryover {$carryover}, Balance {$balance}");
                    }
                } catch (\Throwable $e) {
                    log_message('error', "Billing update exception: " . $e->getMessage());
                }
            }

            if ($toEmail) {
                try {
                    $info = $this->userInfoModel->getByUserId($userId) ?? [];
                    $displayName = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? '')) ?: $toEmail;

                    $siteUrl = rtrim(base_url(), '/');
                    $accountUrl = $siteUrl . '/account';
                    $supportUrl = $siteUrl . '/contact';
                    $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: 'no-reply@localhost';
                    $fromName = getenv('MAIL_FROM_NAME') ?: 'Support';

                    $displayNameEsc = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                    $amount = number_format((float)$payment['amount'], 2);
                    $paidAt = $payment['paid_at'] ?? date('Y-m-d H:i:s');
                    $refText = $adminRef ?: ($payment['admin_reference'] ?? 'N/A');

                    $apiKey = getenv('BREVO_API_KEY') ?: null;
                    if ($apiKey) {
                        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

                        $subject = 'Payment confirmed';
                        $html = <<<HTML
                        <!doctype html>
                        <html>
                        <head><meta charset="utf-8"></head>
                        <body>
                        <p>Hello {$displayNameEsc},</p>
                        <p>Your GCash payment has been confirmed.</p>
                        <p>Amount: ₱{$amount}</p>
                        <p>Status: {$paymentStatus}</p>
                        <p>Admin Reference: {$refText}</p>
                        <p>Confirmed At: {$paidAt}</p>
                        <p>Thank you.</p>
                        </body>
                        </html>
                        HTML;

                        $plain = "Hello {$displayName},\n\nYour GCash payment has been confirmed.\nAmount: ₱{$amount}\nStatus: {$paymentStatus}\nAdmin Reference: {$refText}\nConfirmed At: {$paidAt}\n\nThis is an automated message.";

                        $email = new \Brevo\Client\Model\SendSmtpEmail([
                            'subject' => $subject,
                            'sender' => ['name' => $fromName, 'email' => $fromEmail],
                            'to' => [['email' => $toEmail, 'name' => $displayName]],
                            'htmlContent' => $html,
                            'textContent' => $plain
                        ]);

                        try {
                            $apiInstance->sendTransacEmail($email);
                            log_message('info', 'GCash confirmation email sent for payment ' . $paymentId);
                        } catch (\Throwable $e) {
                            log_message('error', 'Email sending error: ' . $e->getMessage());
                        }
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'Email error: ' . $e->getMessage());
                }
            }
        }

        $message = $updated ? 'Payment confirmed successfully' : 'Failed to confirm payment';
        return $this->response->setJSON(['success' => (bool)$updated, 'message' => $message]);

    } catch (\Throwable $e) {
        log_message('error', 'confirmGCashPayment exception: ' . $e->getMessage());
        return $this->response->setJSON(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    }
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

            // If we successfully updated the payment record, try to notify the user by email (non-blocking)
            if ($updated) {
                try {
                    // Fetch payment and user info
                    $payment = $this->paymentsModel->find($paymentId);
                    $userId = $payment['user_id'] ?? null;
                    $toEmail = $payment['email'] ?? null;

                    if (!$toEmail && $userId) {
                        $u = $this->usersModel->find($userId);
                        $toEmail = $u['email'] ?? null;
                    }

                    if ($toEmail) {
                        // Prepare display name
                        $info = [];
                        try {
                            $info = $this->userInfoModel->getByUserId($userId) ?? [];
                        } catch (\Throwable $_) {
                            $info = [];
                        }
                        $displayName = trim(($info['first_name'] ?? '') . ' ' . ($info['last_name'] ?? '')) ?: ($toEmail);

                        // Prepare email values
                        $siteUrl = rtrim(base_url(), '/');
                        $supportUrl = $siteUrl . '/contact';
                        $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
                        $fromName  = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';
                        $displayNameEsc = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                        $supportUrlEsc = htmlspecialchars($supportUrl, ENT_QUOTES, 'UTF-8');
                        $fromNameEsc = htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8');

                        $amount = isset($payment['amount']) ? number_format((float)$payment['amount'], 2) : '-';
                        $refText = $adminRef ? htmlspecialchars($adminRef, ENT_QUOTES, 'UTF-8') : 'N/A';

                        // Send rejection email via Brevo (non-blocking)
                        $apiKey = getenv('BREVO_API_KEY') ?: null;
                        if ($apiKey) {
                            $config = \Brevo\Client\Configuration::getDefaultConfiguration()
                                ->setApiKey('api-key', $apiKey);
                            $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

                            $subject = 'Your payment has been rejected';

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
                                <td align ="center" style="padding:24px 12px;">
                                    <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 18px rgba(16,24,40,0.08);">
                                    <tr>
                                        <td style="padding:28px 32px 16px;">
                                        <h2 style="margin:0 0 8px;font-weight:600;color:#111827;font-size:20px;">Payment Update</h2>
                                        <p style="margin:0;color:#6b7280;">Hello {$displayNameEsc},</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:0 32px 24px;color:#374151;">
                                        <p style="margin:0 0 12px;line-height:1.5;">We reviewed the GCash payment you submitted and it has been rejected by our administrator. Details below:</p>
                                        <p style="margin:6px 0;"><strong>Amount:</strong> ₱{$amount}</p>
                                        <p style="margin:6px 0;"><strong>Admin Reference:</strong> {$refText}</p>
                                        <p style="margin:12px 0 20px;line-height:1.5;">If you believe this was done in error, please re-upload a valid proof of payment or contact our support team for assistance.</p>
                                        <p style="margin:0 0 20px;">
                                            <a href="{$supportUrlEsc}" style="display:inline-block;padding:10px 18px;background:#2563eb;color:#ffffff;border-radius:6px;text-decoration:none;font-weight:600;">Contact Support</a>
                                        </p>
                                        <p style="margin:0;color:#9ca3af;font-size:12px;">If the button above does not work, copy and paste this link into your browser: {$supportUrlEsc}</p>
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

                            $plain = "Hello {$displayName},\n\nYour GCash payment has been rejected.\n\nAmount: ₱{$amount}\nAdmin Reference: {$adminRef}\n\nIf you believe this is an error, please contact support: {$supportUrl}\n\nThis is an automated message.";

                            $email = new \Brevo\Client\Model\SendSmtpEmail([
                                'subject' => $subject,
                                'sender' => ['name' => $fromName, 'email' => $fromEmail],
                                'to' => [['email' => $toEmail, 'name' => $displayName]],
                                'htmlContent' => $html,
                                'textContent' => $plain
                            ]);

                            $apiInstance->sendTransacEmail($email);
                        } else {
                            log_message('warning', 'BREVO_API_KEY not configured — skipping GCash rejection email for payment ' . $paymentId . ' (user ' . ($toEmail ?? 'unknown') . ')');
                        }
                    } else {
                        log_message('warning', 'No recipient email found for rejected GCash payment ID ' . $paymentId);
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'Reject GCash email failed for payment ' . $paymentId . ': ' . $e->getMessage());
                }
            }

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

        $amountPaid = floatval($data['amount']);

        $adminRef = $data['admin_reference'] ?? ('CNT-' . date('YmdHis') . '-' . rand(100, 999));

        // Insert payment record (keep original fields)
        $paymentId = $paymentsModel->insert([
            'user_id'           => $data['user_id'],
            'billing_id'        => $data['billing_id'],
            'amount'            => $amountPaid,
            'method'            => 'offline',  // original
            'status'            => 'Paid',     // will update later if partial
            'payment_intent_id' => null,
            'payment_method_id' => null,
            'reference_number'  => null,
            'admin_reference'   => $adminRef,
            'paid_at'           => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s')
        ], true);

        // --- Apply carryover and balance logic ---
        $carryover = floatval($billing['carryover'] ?? 0);
        $balance   = floatval($billing['balance'] ?? $billing['amount_due']);

        // Deduct from carryover first
        if ($carryover > 0) {
            $deduct = min($amountPaid, $carryover);
            $carryover -= $deduct;
            $amountPaid -= $deduct;
        }

        // Deduct from balance next
        if ($amountPaid > 0 && $balance > 0) {
            $deduct = min($amountPaid, $balance);
            $balance -= $deduct;
            $amountPaid -= $deduct;
        }

        // Determine billing status
        $billingStatus = ($balance <= 0 && $carryover <= 0) ? 'Paid' : 'Partial';

        // Update billing
        $billingModel->update($data['billing_id'], [
            'carryover' => $carryover,
            'balance'   => $balance,
            'status'    => $billingStatus,
            'paid_date' => ($billingStatus === 'Paid') ? date('Y-m-d H:i:s') : null
        ]);

        // Update payment status accordingly
        $paymentsModel->update($paymentId, [
            'status' => strtolower($billingStatus)
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'billing_status' => $billingStatus
        ]);

    } catch (\Exception $e) {
        log_message('error', $e->getMessage());
        return $this->response->setJSON(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
}

    // Private method: carryover and balance deduction logic
    private function computeCarryoverAndBalance(array $billing, float $amountPaid): array
    {
        $carryover = floatval($billing['carryover'] ?? 0);
        $balance   = floatval($billing['balance'] ?? 0);

        // Deduct from carryover first
        if ($carryover > 0) {
            $deduct = min($amountPaid, $carryover);
            $carryover -= $deduct;
            $amountPaid -= $deduct;
        }

        // Deduct from balance next
        if ($amountPaid > 0 && $balance > 0) {
            $deduct = min($amountPaid, $balance);
            $balance -= $deduct;
            $amountPaid -= $deduct;
        }

        $status = ($carryover <= 0 && $balance <= 0) ? 'Paid' : 'Partial';

        return [
            'carryover' => $carryover,
            'balance'   => $balance,
            'status'    => $status
        ];
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
            ->where('billings.status', 'Paid')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->get()->getRow()->c ?? 0);

        $partialCount = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('billings.status', 'Partial')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->get()->getRow()->c ?? 0);

        $pendingCount = (int)($this->billingModel
            ->where('billings.status', 'Pending')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->countAllResults());

        $latePayments = (int)$this->billingModel
            ->where('billings.status', 'Pending')
            ->where('due_date <', date('Y-m-d'))
            ->where('DATE(due_date) >=', $startDate)
            ->where('DATE(due_date) <=', $endDate)
            ->countAllResults();

        // Monthly amounts and collection rate in selected window (paid unique households / total)
        $monthlyAmounts = array_fill(1, 12, 0.0);
        $monthlyRates = array_fill(1, 12, 0.0);
        // Aggregate by month within the date window
        $rows = $this->billingModel
            ->select("YEAR(billings.updated_at) as y, MONTH(billings.updated_at) as m, SUM(amount_due) as amt, COUNT(DISTINCT user_id) as pc")
            ->where('billings.status', 'Paid')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->groupBy('YEAR(billings.updated_at), MONTH(billings.updated_at)')
            ->orderBy('YEAR(billings.updated_at)', 'ASC')
            ->orderBy('MONTH(billings.updated_at)', 'ASC')
            ->get()->getResultArray();
        foreach ($rows as $r) {
            $m = (int)$r['m'];
            $monthlyAmounts[$m] = (float)($r['amt'] ?? 0);
            $paidC = (int)($r['pc'] ?? 0);
            $monthlyRates[$m] = $totalHouseholds > 0 ? round(($paidC / $totalHouseholds) * 100, 1) : 0.0;
        }

        // Ensure collection summary values exist for printable/export views
        $currentMonthCollected = (float)($this->billingModel
            ->where('billings.status', 'Paid')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0);

        $pendingAmount = (float)($this->billingModel
            ->where('billings.status', 'Pending')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
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
                ->select('user_id, billings.updated_at as updated_at, amount_due, status')
                ->where('DATE(billings.updated_at) >=', $startDate)
                ->where('DATE(billings.updated_at) <=', $endDate)
                ->where('billings.status', 'Paid')
                ->orderBy('user_id','ASC')
                ->findAll();

            // Also fetch partial payments so exports (PDF/XLSX/CSV) can include the details
            try {
                $partialRows = $this->billingModel
                    ->select('billings.id, billings.bill_no, billings.user_id, billings.amount_due, billings.balance, billings.carryover, billings.status, billings.updated_at, users.email, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
                    ->join('users', 'users.id = billings.user_id', 'left')
                    ->join('user_information', 'user_information.user_id = billings.user_id', 'left')
                    ->where('billings.status', 'Partial')
                    ->where('DATE(billings.updated_at) >=', $startDate)
                    ->where('DATE(billings.updated_at) <=', $endDate)
                    ->orderBy('billings.updated_at', 'DESC')
                    ->get()->getResultArray();
            } catch (\Throwable $_) {
                $partialRows = [];
            }

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
            $renderCollection = function($users, $paidByUser, $startDate, $endDate, $partialRows = []) use ($currentMonthCollected, $paidHouseholds, $totalHouseholds, $pendingAmount, $collectionRate) {
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

                // Partial Payments Details (if any)
                $html .= '<h2 style="margin-top:18px;">Partial Payments Details</h2>';
                if (!empty($partialRows)) {
                    $html .= '<table><thead><tr><th>Bill No</th><th>User ID</th><th>Name</th><th>Email</th><th>Amount Due</th><th>Balance</th><th>Carryover</th><th>Status</th><th>Updated At</th></tr></thead><tbody>';
                    foreach ($partialRows as $r) {
                        $html .= '<tr>' .
                            '<td>' . htmlspecialchars($r['bill_no'] ?? $r['id']) . '</td>' .
                            '<td>' . intval($r['user_id'] ?? 0) . '</td>' .
                            '<td>' . htmlspecialchars($r['name'] ?? '') . '</td>' .
                            '<td>' . htmlspecialchars($r['email'] ?? '') . '</td>' .
                            '<td>' . htmlspecialchars(number_format($r['amount_due'] ?? 0, 2)) . '</td>' .
                            '<td>' . htmlspecialchars(number_format($r['balance'] ?? 0, 2)) . '</td>' .
                            '<td>' . htmlspecialchars(number_format($r['carryover'] ?? 0, 2)) . '</td>' .
                            '<td>' . htmlspecialchars($r['status'] ?? '') . '</td>' .
                            '<td>' . htmlspecialchars($r['updated_at'] ?? '') . '</td>' .
                        '</tr>';
                    }
                    $html .= '</tbody></table>';
                } else {
                    $html .= '<div class="muted">No partial payments found for the selected range.</div>';
                }

                $html .= '</body></html>';
                return $html;
            };

            // Route rendering based on requested type
            if ($type === '') {
                // Export All: combined summary + per-purok
                $htmlSummary = $renderSummary();
                $htmlCollection = $renderCollection($users, $paidByUser, $startDate, $endDate);

                // Helper to extract inner <body> content so we can safely combine two complete HTML documents
                $extractBody = function(string $doc): string {
                    if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $doc, $m)) {
                        return $m[1];
                    }
                    // fallback: remove <!doctype ...> and <html> wrappers crudely
                    $doc = preg_replace('/^\s*<!DOCTYPE[^>]*>/i', '', $doc);
                    $doc = preg_replace('/<\/?html[^>]*>/i', '', $doc);
                    return $doc;
                };

                $bodySummary = $extractBody($htmlSummary);
                $bodyCollection = $extractBody($htmlCollection);

                $combined = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Reports</title>' .
                    '<style>body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:24px;}h1{margin:0 0 6px;font-size:20px}h2{margin:12px 0 6px;}table{width:100%;margin:8px 0;border-collapse:collapse}td,th{padding:6px;font-size:14px} .muted{color:#6b7280}</style>' .
                    '</head><body>' . $bodySummary . '<div style="margin-top:24px;">' . $bodyCollection . '</div></body></html>';

                return $this->response->setHeader('Content-Type', 'text/html')->setBody($combined);
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
            try {
                // Use PhpSpreadsheet to build a workbook. If exporting collection detail, produce one sheet per purok.
                $spreadsheet = new Spreadsheet();

                // Build a Monthly sheet
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Monthly');
                $sheet->setCellValue('A1', 'Month');
                $sheet->setCellValue('B1', 'Collection Rate (%)');
                $sheet->setCellValue('C1', 'Amount (PHP)');
                $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                for ($i = 1; $i <= 12; $i++) {
                    $row = $i + 1;
                    $sheet->setCellValue('A' . $row, $months[$i-1]);
                    $sheet->setCellValue('B' . $row, (float)($monthlyRates[$i] ?? 0));
                    $sheet->setCellValue('C' . $row, (float)($monthlyAmounts[$i] ?? 0));
                }

                // Summary sheet
                $sum = $spreadsheet->createSheet();
                $sum->setTitle('Summary');
                $sum->fromArray([
                    ['Metric', 'Value'],
                    ['Paid Households', $paidHouseholds],
                    ['Partial (distinct users)', $partialCount],
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

                // If collection detail requested or Export All, add Partial Payments and per-purok sheets
                if ($type === 'collection' || $type === '') {
                    // Partial Payments sheet
                    $partialSheet = $spreadsheet->createSheet();
                    $partialSheet->setTitle('Partial Payments');
                    $partialSheet->fromArray([
                        ['Bill No', 'User ID', 'Name', 'Email', 'Amount Due', 'Balance', 'Carryover', 'Status', 'Updated At']
                    ], null, 'A1');
                    $r = 2;
                    foreach ($partialRows as $pr) {
                        $partialSheet->setCellValue('A' . $r, $pr['bill_no'] ?? $pr['id']);
                        $partialSheet->setCellValue('B' . $r, $pr['user_id'] ?? '');
                        $partialSheet->setCellValue('C' . $r, $pr['name'] ?? '');
                        $partialSheet->setCellValue('D' . $r, $pr['email'] ?? '');
                        $partialSheet->setCellValue('E' . $r, (float)($pr['amount_due'] ?? 0));
                        $partialSheet->setCellValue('F' . $r, (float)($pr['balance'] ?? 0));
                        $partialSheet->setCellValue('G' . $r, (float)($pr['carryover'] ?? 0));
                        $partialSheet->setCellValue('H' . $r, $pr['status'] ?? '');
                        $partialSheet->setCellValue('I' . $r, $pr['updated_at'] ?? '');
                        $r++;
                    }

                    // Build per-Purok sheets with paid details
                    $byPurok = [];
                    foreach ($users as $u) {
                        $p = $u['purok'] ?? 'Unspecified';
                        if ($p === '' || $p === null) $p = 'Unspecified';
                        $byPurok[$p][] = $u;
                    }
                    foreach ($byPurok as $purok => $plist) {
                        $title = 'Purok ' . $purok;
                        $ws = $spreadsheet->createSheet();
                        // sanitize sheet title length
                        $ws->setTitle(substr($title, 0, 31));
                        $ws->fromArray([['User ID','Name','Paid?','Paid Dates','Amounts (PHP)']], null, 'A1');
                        $rr = 2;
                        foreach ($plist as $u) {
                            $uid = (int)$u['user_id'];
                            $paidRowsForUser = $paidByUser[$uid] ?? [];
                            if (!empty($paidRowsForUser)) {
                                $dates = array_map(function($r){ return $r['updated_at']; }, $paidRowsForUser);
                                $amounts = array_map(function($r){ return number_format($r['amount_due'],2,'.',''); }, $paidRowsForUser);
                                $dateStr = implode('; ', $dates);
                                $amtStr = implode('; ', $amounts);
                                $paid = 'Yes';
                            } else {
                                $paid = 'No';
                                $dateStr = '';
                                $amtStr = '';
                            }
                            $ws->setCellValue('A' . $rr, $uid);
                            $ws->setCellValue('B' . $rr, $u['name'] ?? '');
                            $ws->setCellValue('C' . $rr, $paid);
                            $ws->setCellValue('D' . $rr, $dateStr);
                            $ws->setCellValue('E' . $rr, $amtStr);
                            $rr++;
                        }
                    }
                }
                $filename = $fileBase . '.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                $writer = new Xlsx($spreadsheet);
                $writer->setIncludeCharts(true);
                $writer->save('php://output');
                exit;
            } catch (\Throwable $e) {
                log_message('error', 'Export XLSX error: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to generate XLSX: ' . $e->getMessage()]);
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
            try {
                $filename = isset($fileBase) ? ($fileBase . (($format === 'excel') ? '.xls' : '.csv')) : ('export.csv');
                log_message('debug', '[ExportReports] CSV export started');
                $csvContent = '';
                $errorMsg = null;
                $fh = fopen('php://temp', 'w+');
                try {
                    // Monthly Collection
                    fputcsv($fh, ['Section', 'Monthly Collection (' . $year . ')']);
                    fputcsv($fh, ['Month', 'Collection Rate (%)', 'Amount (PHP)']);
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    for ($i = 1; $i <= 12; $i++) {
                        fputcsv($fh, [$months[$i-1], $monthlyRates[$i], number_format($monthlyAmounts[$i], 2, '.', '')]);
                    }
                    fputcsv($fh, []);

                    // Payment Status (selected range)
                    fputcsv($fh, ['Section', 'Payment Status (Selected Range)']);
                    fputcsv($fh, ['Paid Households', 'Partial', 'Pending', 'Late']);
                    fputcsv($fh, [$paidHouseholds, $partialCount, $pendingCount, $latePayments]);
                    fputcsv($fh, []);

                    // Partial Payments Details (list billings with status Partial)
                    try {
                        $partialRows = $this->billingModel
                            ->select('billings.id, billings.bill_no, billings.user_id, billings.amount_due, billings.balance, billings.carryover, billings.status, billings.updated_at, users.email, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
                            ->join('users', 'users.id = billings.user_id', 'left')
                            ->join('user_information', 'user_information.user_id = billings.user_id', 'left')
                            ->where('billings.status', 'Partial')
                            ->where('DATE(billings.updated_at) >=', $startDate)
                            ->where('DATE(billings.updated_at) <=', $endDate)
                            ->orderBy('billings.updated_at', 'DESC')
                            ->get()->getResultArray();
                    } catch (\Throwable $dbErr) {
                        $partialRows = [];
                        $errorMsg = 'Error fetching partial payments: ' . $dbErr->getMessage();
                        log_message('error', '[ExportReports] CSV DB error: ' . $dbErr->getMessage());
                    }

                    fputcsv($fh, ['Section', 'Partial Payments Details']);
                    fputcsv($fh, ['Bill No', 'User ID', 'Name', 'Email', 'Amount Due', 'Balance', 'Carryover', 'Status', 'Updated At']);
                    if ($errorMsg) {
                        fputcsv($fh, [$errorMsg]);
                    } else {
                        foreach ($partialRows as $r) {
                            fputcsv($fh, [
                                $r['bill_no'] ?? $r['id'],
                                $r['user_id'] ?? '',
                                $r['name'] ?? '',
                                $r['email'] ?? '',
                                number_format($r['amount_due'] ?? 0, 2, '.', ''),
                                number_format($r['balance'] ?? 0, 2, '.', ''),
                                number_format($r['carryover'] ?? 0, 2, '.', ''),
                                $r['status'] ?? '',
                                $r['updated_at'] ?? ''
                            ]);
                        }
                    }
                    fputcsv($fh, []);
                } catch (\Throwable $csvErr) {
                    $errorMsg = 'Error generating CSV: ' . $csvErr->getMessage();
                    log_message('error', '[ExportReports] CSV generation error: ' . $csvErr->getMessage());
                    fputcsv($fh, ['Error', $errorMsg]);
                }

                rewind($fh);
                $csvContent = stream_get_contents($fh);
                fclose($fh);

                // Return CSV as framework response to avoid mixed header/output issues
                log_message('debug', '[ExportReports] CSV export finished');
                return $this->response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csvContent);
            } catch (\Throwable $e) {
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
                log_message('error', 'Export CSV error: ' . $e->getMessage());
                echo json_encode(['error' => 'Failed to generate CSV: ' . $e->getMessage()]);
                exit;
            }
        } else {
            header('Content-Type: text/csv');
        }
        // Ensure filename is defined for downstream CSV output
        if (!isset($filename) || empty($filename)) {
            $filename = isset($fileBase) ? ($fileBase . (($format === 'excel') ? '.xls' : '.csv')) : 'export.csv';
        }
        // Build CSV content into memory and return via framework response
        if (!isset($filename) || empty($filename)) {
            $filename = isset($fileBase) ? ($fileBase . (($format === 'excel' || $format === 'xlsx') ? '.xlsx' : '.csv')) : 'export.csv';
        }

        $outStream = fopen('php://temp', 'w+');
        // Monthly Collection
        fputcsv($outStream, ['Section', 'Monthly Collection (' . $year . ')']);
        fputcsv($outStream, ['Month', 'Collection Rate (%)', 'Amount (PHP)']);
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        for ($i = 1; $i <= 12; $i++) {
            fputcsv($outStream, [$months[$i-1], $monthlyRates[$i], number_format($monthlyAmounts[$i], 2, '.', '')]);
        }
        fputcsv($outStream, []);

        // Payment Status (selected range)
        fputcsv($outStream, ['Section', 'Payment Status (Selected Range)']);
        fputcsv($outStream, ['Paid Households', 'Partial', 'Pending', 'Late']);
        fputcsv($outStream, [$paidHouseholds, $partialCount, $pendingCount, $latePayments]);
        fputcsv($outStream, []);

        // Partial Payments Details (list billings with status Partial)
        $partialRows = $this->billingModel
            ->select('billings.id, billings.bill_no, billings.user_id, billings.amount_due, billings.balance, billings.carryover, billings.status, billings.updated_at, users.email, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = billings.user_id', 'left')
            ->where('billings.status', 'Partial')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->orderBy('billings.updated_at', 'DESC')
            ->get()->getResultArray();

        fputcsv($outStream, ['Section', 'Partial Payments Details']);
        fputcsv($outStream, ['Bill No', 'User ID', 'Name', 'Email', 'Amount Due', 'Balance', 'Carryover', 'Status', 'Updated At']);
        foreach ($partialRows as $r) {
            fputcsv($outStream, [
                $r['bill_no'] ?? $r['id'],
                $r['user_id'] ?? '',
                $r['name'] ?? '',
                $r['email'] ?? '',
                number_format($r['amount_due'] ?? 0, 2, '.', ''),
                number_format($r['balance'] ?? 0, 2, '.', ''),
                number_format($r['carryover'] ?? 0, 2, '.', ''),
                $r['status'] ?? '',
                $r['updated_at'] ?? ''
            ]);
        }
        fputcsv($outStream, []);

        // Rate Distribution (households)
        fputcsv($outStream, ['Section', 'Rate Distribution (Households)']);
        fputcsv($outStream, ['Normal', 'Senior', 'Alone', 'Total Households']);
        fputcsv($outStream, [$normalCount, $seniorCount, $aloneCount, $totalHouseholds]);
        fputcsv($outStream, []);

        // Fixed Rates
        fputcsv($outStream, ['Section', 'Fixed Rates (PHP)']);
        fputcsv($outStream, ['Normal', 'Senior', 'Alone']);
        fputcsv($outStream, [number_format($rateNormal,2,'.',''), number_format($rateSenior,2,'.',''), number_format($rateAlone,2,'.','')]);

        // If exporting collection details or exporting All, append per-purok detail rows (tables)
        if ($type === '' || $type === 'collection') {
            fputcsv($outStream, []);
            fputcsv($outStream, ['Section', 'Collection Details (Per Purok)']);
            fputcsv($outStream, ['Purok', 'User ID', 'Name', 'Paid?', 'Paid Dates', 'Amounts (PHP)']);

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
                        ->where('billings.status', 'Paid')
                    ->where('DATE(billings.updated_at) >=', $startDate)
                    ->where('DATE(billings.updated_at) <=', $endDate)
                    ->orderBy('billings.updated_at','ASC')
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
                fputcsv($outStream, [$purok, $uid, ($u['name'] ?? 'Unknown'), $paid, $dateStr, $amtStr]);
            }
        }

        rewind($outStream);
        $csvContent = stream_get_contents($outStream);
        fclose($outStream);

        return $this->response->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csvContent);
    }

    

    // ---------------- Billing Functions ----------------


     //Display billing management page
    public function billingManagement()
    {
        return view('admin/billing_management');
    }

    // Get all billings (AJAX endpoint for billing management)

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

// Synchronize billings (automatic billing) with carryover and email notifications
public function synchronizeBillings()
{
    try {
        $input = $this->request->getJSON(true);
        $month = $input['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
 
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid month format'
            ]);
        }

        // Check if billings for this month already exist
        $existingBillingsForMonth = $this->billingModel
            ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
            ->countAllResults();

        if ($existingBillingsForMonth > 0) {
            $monthName = date('F Y', strtotime($month . '-01'));
            $approvedUsersCount = $this->usersModel
                ->whereIn('status', ['approved', 'Approved'])
                ->countAllResults();

            return $this->response->setJSON([
                'success' => false,
                'message' => "⚠️ Billing synchronization for {$monthName} has already been completed! Found {$existingBillingsForMonth} existing billings out of {$approvedUsersCount} approved users."
            ]);
        }

        // Fetch approved users
        $users = $this->usersModel
            ->select('users.id, users.status, users.email, user_information.family_number, user_information.age, user_information.first_name, user_information.last_name')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->whereIn('users.status', ['approved', 'Approved'])
            ->findAll();

        if (empty($users)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No approved users found for billing synchronization.'
            ]);
        }

        $created = 0;
        $existing = 0;
        $processed = 0;
        $skipped = 0;
        $emailsSent = 0;

        foreach ($users as $user) {
            $processed++;

            $familyNumber = max(1, (int) ($user['family_number'] ?? 1));
            $age = max(1, (int) ($user['age'] ?? 30));
            $amount = $this->calculateBillingAmount(['family_number' => $familyNumber, 'age' => $age]);

            // Skip if billing already exists for this month
            $existingBilling = $this->billingModel
                ->where('user_id', $user['id'])
                ->where('DATE_FORMAT(billing_month, "%Y-%m")', $month)
                ->first();

            if ($existingBilling) {
                $existing++;
                $skipped++;
                continue;
            }

            $syncDate = date('Y-m-d H:i:s');
            $dueDate = date('Y-m-d', strtotime('+7 days'));
            $billNo = 'BILL-' . date('Ymd') . '-' . str_pad($user['id'], 4, '0', STR_PAD_LEFT);

            // --- Get previous billing to calculate carryover ---
            $prevBill = $this->billingModel
                ->where('user_id', $user['id'])
                ->where('billing_month <', $month . '-01')
                ->orderBy('billing_month', 'DESC')
                ->first();

            $carryover = 0.0;
            $balance = $amount;

            if ($prevBill) {
                $prevBalance = floatval($prevBill['balance'] ?? 0);

                // Update previous billing status to Partial if it has unpaid balance
                if ($prevBalance > 0) {
                    $this->billingModel->update($prevBill['id'], [
                        'status' => 'Partial',
                        'updated_at' => $syncDate
                    ]);
                }

                // Carryover for new billing = previous balance
                $carryover = $prevBalance;
                $balance += $carryover;
            }

            $billingData = [
                'user_id' => $user['id'],
                'bill_no' => $billNo,
                'amount_due' => number_format($amount, 2, '.', ''),
                'carryover' => number_format($carryover, 2, '.', ''),
                'balance' => number_format($balance, 2, '.', ''),
                'billing_month' => $month . '-01',
                'due_date' => $dueDate,
                'status' => 'Pending',
                'created_at' => $syncDate,
                'updated_at' => $syncDate
            ];

            if ($this->billingModel->insert($billingData)) {
                $created++;

                // Send email notification
                if (!empty($user['email'])) {
                    $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Valued Customer';
                    $this->sendBillingNotificationEmail(
                        $user['email'],
                        $userName,
                        $billNo,
                        $balance,
                        $dueDate,
                        $month . '-01'
                    );
                    $emailsSent++;
                }
            } else {
                $skipped++;
            }
        }

        $monthName = date('F Y', strtotime($month . '-01'));
        $message = "✅ Billing synchronization for {$monthName} completed. Processed: {$processed}, Created: {$created}, Emails sent: {$emailsSent}";
        if ($existing > 0) $message .= ", Skipped (existing): {$existing}";
        if ($skipped > $existing) $message .= ", Failed: " . ($skipped - $existing);

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'stats' => [
                'month' => $monthName,
                'processed' => $processed,
                'created' => $created,
                'existing' => $existing,
                'skipped' => $skipped,
                'emails_sent' => $emailsSent,
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
    $json   = $this->request->getJSON(true) ?? $this->request->getPost();
    $userId = (int) ($json['user_id'] ?? $this->request->getPost('user_id'));
    $month  = trim($json['month'] ?? $this->request->getPost('month') ?? date('Y-m'));
    $amount = isset($json['amount']) ? (float)$json['amount'] : (float)$this->request->getPost('amount');

    if (!$userId || !preg_match('/^\d{4}-\d{2}$/', $month) || $amount <= 0) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid input']);
    }

    $billingMonthFirstDay = $month . '-01';

    // Verify user exists
    $user = $this->usersModel->find($userId);
    if (!$user) {
        return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
    }

    // Prevent duplicate billing for same user+month
    $exists = $this->billingModel
        ->where('user_id', $userId)
        ->where("DATE_FORMAT(billing_month, '%Y-%m')", $month)
        ->first();

    if ($exists) {
        return $this->response->setJSON(['success' => false, 'message' => 'Billing already exists for this user and month']);
    }

    $db = \Config\Database::connect();

    // --- Get the latest previous billing ---
    $prevBill = $db->table('billings')
        ->where('user_id', $userId)
        ->where('billing_month <', $billingMonthFirstDay)
        ->orderBy('billing_month', 'DESC')
        ->get()
        ->getRowArray();

    $carryover = 0.0;
    if ($prevBill) {
        $balancePrev = floatval($prevBill['balance']);
        $carryoverPrev = floatval($prevBill['carryover'] ?? 0);

        // Only set carryover for the new billing
        $carryover = $carryoverPrev + $balancePrev;

        // Update last billing status to Partial if it has unpaid balance
        if ($balancePrev > 0) {
            $this->billingModel->update($prevBill['id'], [
                'status' => 'Partial',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    // Build unique bill_no
    $datePrefix = 'MANUAL-' . date('Ymd');
    $maxAttempts = 50;
    $billNo = null;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $suffix = str_pad($attempt, 4, '0', STR_PAD_LEFT);
        $candidate = $datePrefix . '-' . $suffix;
        if (!$this->billingModel->where('bill_no', $candidate)->first()) {
            $billNo = $candidate;
            break;
        }
    }
    if ($billNo === null) {
        $billNo = $datePrefix . '-' . uniqid();
    }

    // New billing: balance = new amount, carryover = sum from last billing
    $newBilling = [
        'user_id'       => $userId,
        'bill_no'       => $billNo,
        'amount_due'    => number_format($amount, 2, '.', ''),
        'carryover'     => number_format($carryover, 2, '.', ''), 
        'balance'       => number_format($amount, 2, '.', ''),   
        'status'        => 'Pending',
        'billing_month' => $billingMonthFirstDay,
        'due_date'      => date('Y-m-d', strtotime($billingMonthFirstDay . ' +7 days')),
        'created_at'    => date('Y-m-d H:i:s'),
        'updated_at'    => date('Y-m-d H:i:s'),
    ];

    try {
        $inserted = $this->billingModel->insert($newBilling);

        if ($inserted) {
            // Send email notification
            if (!empty($user['email'])) {
                $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Valued Customer';
                $this->sendBillingNotificationEmail(
                    $user['email'],
                    $userName,
                    $billNo,
                    $amount,
                    $newBilling['due_date'],
                    $billingMonthFirstDay
                );
            }

            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Manual billing created and email sent',
                'bill_no'    => $billNo,
                'billing'    => [
                    'user_id'       => $userId,
                    'bill_no'       => $billNo,
                    'amount_due'    => number_format($amount, 2, '.', ''),
                    'balance'       => number_format($amount, 2, '.', ''),
                    'carryover'     => number_format($carryover, 2, '.', ''),
                    'total_due'     => number_format($carryover + $amount, 2, '.', ''),
                    'billing_month' => $billingMonthFirstDay,
                    'due_date'      => $newBilling['due_date'],
                    'status'        => $newBilling['status'],
                ]
            ]);
        }
    } catch (\Throwable $e) {
        log_message('error', 'createManualBilling insert error: ' . $e->getMessage());
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create manual billing: ' . $e->getMessage()]);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Failed to create manual billing']);
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
            ->where('billings.status', 'Paid')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0;
        
        // Paid households in range (distinct users), based on updated_at
        $paidHouseholds = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('billings.status', 'Paid')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->get()
            ->getRow()->c ?? 0);

        // Partial payments count (distinct users with status 'Partial')
        $partialCount = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('billings.status', 'Partial')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->get()
            ->getRow()->c ?? 0);

        // Partial payments: distinct users with billing status 'Partial' in range
        $partialCount = (int)($this->billingModel
            ->select('COUNT(DISTINCT user_id) as c')
            ->where('billings.status', 'Partial')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->get()
            ->getRow()->c ?? 0);
        
        // Pending amount and count (range)
        $pendingAmount = (float)($this->billingModel
            ->where('billings.status', 'Pending')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->selectSum('amount_due')
            ->get()
            ->getRow()
            ->amount_due ?? 0);

        $pendingCount = (int)($this->billingModel
            ->where('billings.status', 'Pending')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->countAllResults());
        
        // Late payments (overdue)
        $latePayments = $this->billingModel
            ->where('billings.status', 'Pending')
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
            ->select("YEAR(billings.updated_at) as y, MONTH(billings.updated_at) as month_num, COUNT(DISTINCT user_id) as paid_count, SUM(amount_due) as total_amount")
            ->where('billings.status', 'Paid')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->groupBy('YEAR(billings.updated_at), MONTH(billings.updated_at)')
            ->orderBy('YEAR(billings.updated_at)', 'ASC')
            ->orderBy('MONTH(billings.updated_at)', 'ASC')
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
                ->select("DATE_FORMAT(billings.updated_at, '%Y-%m') as ym, MONTH(billings.updated_at) as month_num, COUNT(DISTINCT user_id) as paid_count, SUM(amount_due) as total_amount")
                ->where('billings.status', 'Paid')
                ->where('DATE(billings.updated_at) >=', $twelveMonthsAgo)
                ->groupBy('YEAR(billings.updated_at), MONTH(billings.updated_at)')
                ->orderBy('YEAR(billings.updated_at)', 'ASC')
                ->orderBy('MONTH(billings.updated_at)', 'ASC')
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

        // If still no paid billings were recorded in the range, fall back to payments table
        // (Some flows mark payments as 'paid' before updating billing to Paid — use payments to surface collection activity)
        if (array_sum($collectionRates) === 0) {
            $paymentsMonthly = [];
            try {
                // Use COALESCE(paid_at, created_at) and accept several paid-status casings
                $paymentsMonthly = $this->paymentsModel
                    ->select("MONTH(COALESCE(paid_at, created_at)) as month_num, COUNT(DISTINCT user_id) as paid_count")
                    ->where("(LOWER(status) = 'paid' OR status = 'Paid' OR paid_at IS NOT NULL)")
                    ->where('DATE(COALESCE(paid_at, created_at)) >=', $startDate)
                    ->where('DATE(COALESCE(paid_at, created_at)) <=', $endDate)
                    ->groupBy('MONTH(COALESCE(paid_at, created_at))')
                    ->orderBy('MONTH(COALESCE(paid_at, created_at))', 'ASC', false)
                    ->get()
                    ->getResultArray();

                foreach ($paymentsMonthly as $row) {
                    $idx = (int)$row['month_num'] - 1;
                    if ($idx < 0 || $idx > 11) continue;
                    $pc = (int)$row['paid_count'];
                    $collectionRates[$idx] = $totalHouseholds > 0 ? round(($pc / $totalHouseholds) * 100, 1) : 0;
                }
            } catch (\Exception $e) {
                // Log and continue — keep collectionRates as zeros if payments query fails
                log_message('error', 'reports(): fallback payments query failed - ' . $e->getMessage());
            }
            // Debug: log what we found
            log_message('debug', 'reports(): totalHouseholds=' . intval($totalHouseholds) . ', start=' . $startDate . ', end=' . $endDate);
            log_message('debug', 'reports(): paymentsMonthly=' . json_encode($paymentsMonthly));
            log_message('debug', 'reports(): collectionRates after fallback=' . json_encode($collectionRates));

            // If still zero, try computing collection rate by amount collected vs expected monthly
            if (array_sum($collectionRates) === 0) {
                try {
                    $paymentsAmountMonthly = $this->paymentsModel
                        ->select("MONTH(COALESCE(paid_at, created_at)) as month_num, SUM(amount) as total_amount")
                        ->where("(LOWER(status) = 'paid' OR status = 'Paid' OR paid_at IS NOT NULL OR LOWER(status) = 'partial')")
                        ->where('DATE(COALESCE(paid_at, created_at)) >=', $startDate)
                        ->where('DATE(COALESCE(paid_at, created_at)) <=', $endDate)
                        ->groupBy('MONTH(COALESCE(paid_at, created_at))')
                        ->orderBy('MONTH(COALESCE(paid_at, created_at))', 'ASC', false)
                        ->get()
                        ->getResultArray();

                    if (!empty($paymentsAmountMonthly)) {
                        foreach ($paymentsAmountMonthly as $row) {
                            $idx = (int)$row['month_num'] - 1;
                            if ($idx < 0 || $idx > 11) continue;
                            $amt = (float)$row['total_amount'];
                            $pct = ($monthlyExpected > 0) ? round((($amt) / $monthlyExpected) * 100, 1) : 0;
                            $collectionRates[$idx] = min(100, $pct);
                        }
                        log_message('debug', 'reports(): collectionRates computed from payment amounts=' . json_encode($collectionRates));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'reports(): payments amount fallback failed - ' . $e->getMessage());
                }
            }
        }

        // Compute monthly partial rates (percent of households that are partially paid)
        $partialRates = array_fill(0, 12, 0);
        $partialMonthlyData = $this->billingModel
            ->select("DATE_FORMAT(billings.updated_at, '%Y-%m') as ym, MONTH(billings.updated_at) as month_num, COUNT(DISTINCT billings.user_id) as partial_count")
            ->where('billings.status', 'Partial')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->groupBy('YEAR(billings.updated_at), MONTH(billings.updated_at)')
            ->orderBy('YEAR(billings.updated_at)', 'ASC')
            ->orderBy('MONTH(billings.updated_at)', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($partialMonthlyData as $r) {
            $mn = (int)$r['month_num'];
            $count = (int)$r['partial_count'];
            $idx = max(0, ($mn - 1) % 12);
            $partialRates[$idx] = $totalHouseholds > 0 ? round(($count / $totalHouseholds) * 100, 1) : 0;
        }

        // Fetch partial bills (for display in reports)
        $partialBills = $this->billingModel
            ->select('billings.*, users.email, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = billings.user_id', 'left')
            ->where('billings.status', 'Partial')
            ->where('DATE(billings.updated_at) >=', $startDate)
            ->where('DATE(billings.updated_at) <=', $endDate)
            ->orderBy('billings.updated_at', 'DESC')
            ->get()->getResultArray();

        // Normalize fields so views can rely on consistent keys
        if (is_array($partialBills)) {
            $partialBills = $this->normalizeBillingRows($partialBills);
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
            'partialCount' => $partialCount,
            'pendingAmount' => $pendingAmount,
            'pendingCount' => $pendingCount,
            'latePayments' => $latePayments,
            'collectionRate' => $collectionRate,
            'collectionRates' => $collectionRates,
            'partialRates' => $partialRates,
            'collectionAmounts' => $collectionAmounts,
            'statusScope' => $statusScope,
            'partialBills' => $partialBills,
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

    // Admin check page: list all partial/non-paid billings
    public function partialBillings()
    {
        $partialBills = $this->billingModel
            ->select('billings.*, users.email, CONCAT(user_information.first_name, " ", user_information.last_name) as name')
            ->join('users', 'users.id = billings.user_id', 'left')
            ->join('user_information', 'user_information.user_id = billings.user_id', 'left')
            // Only show billings with status Partial
            ->where('billings.status', 'Partial')
            ->orderBy('billings.updated_at', 'DESC')
            // Prevent duplicate rows in case of multiple joined information rows
            ->groupBy('billings.id')
            ->get()->getResultArray();

        // Defensive server-side deduplication: sometimes joins or data anomalies
        // produce duplicated rows. Ensure unique by billing `id` while keeping
        // the first (most recent) row encountered.
        $originalCount = is_array($partialBills) ? count($partialBills) : 0;
        $byId = [];
        if (is_array($partialBills)) {
            foreach ($partialBills as $row) {
                $id = $row['id'] ?? null;
                if ($id === null) {
                    // if no id present, push as-is
                    $byId[] = $row;
                    continue;
                }
                if (!isset($byId[$id])) {
                    $byId[$id] = $row; // keep first occurrence (ordered by updated_at desc)
                }
            }
        }
        // Normalize back to indexed array preserving the encountered order
        $partialBills = array_values($byId);
        log_message('debug', '[Admin::partialBillings] partialBills original=' . $originalCount . ' deduped=' . count($partialBills));

        return view('admin/partialBillings', [
            'partialBills' => $partialBills
        ]);
    }

    /**
     * Normalize billing rows to include consistent keys used by views.
     * - household_name: populated from user info name
     * - account_no: fallback to bill_no
     * - amount_due, amount_paid, balance: numeric values
     * - last_payment_at: prefer billings.paid_date or payments.paid_at when available (controller uses paid_date)
     */
    private function normalizeBillingRows(array $rows): array
    {
        return array_map(function($r) {
            $amountDue = isset($r['amount_due']) ? (float)$r['amount_due'] : (isset($r['amount']) ? (float)$r['amount'] : 0.0);
            $balance = isset($r['balance']) ? (float)$r['balance'] : null;
            // If balance is null, try to compute from payments if amount_paid present
            if ($balance === null) {
                if (isset($r['amount_paid'])) {
                    $balance = $amountDue - (float)$r['amount_paid'];
                } else {
                    $balance = $amountDue; // assume nothing paid
                }
            }
            $amountPaid = $amountDue - $balance;

            $household = $r['household_name'] ?? $r['name'] ?? trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: null;
            $account = $r['account_no'] ?? $r['account'] ?? $r['bill_no'] ?? ($r['id'] ?? null);

            // Last payment timestamp preference: billings.paid_date then payments.paid_at then custom keys
            $lastPayment = $r['paid_date'] ?? $r['paid_at'] ?? $r['last_payment_at'] ?? null;

            // Coerce types and set defaults
            $r['household_name'] = $household;
            $r['account_no'] = $account;
            $r['amount_due'] = $amountDue;
            $r['balance'] = $balance;
            $r['amount_paid'] = $amountPaid >= 0 ? $amountPaid : 0.0;
            $r['last_payment_at'] = $lastPayment;

            return $r;
        }, $rows);
    }

    /**
     * Temporary diagnostics endpoint to help investigate duplicate rows and
     * status distribution for billings. Intended for local/dev use only.
     * Example: /admin/partialBillingsDiagnostics?start=2025-11-01&end=2025-11-25
     */
    public function partialBillingsDiagnostics()
    {
        // Disallow in production environments
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Not allowed in production.']);
        }

        $start = trim((string)$this->request->getGet('start')) ?: date('Y-m-d', strtotime('-30 days'));
        $end = trim((string)$this->request->getGet('end')) ?: date('Y-m-d');

        try {
            $db = \Config\Database::connect();

            // 1) status distribution
            $dist = $this->billingModel->select('status, COUNT(*) as cnt')->groupBy('status')->get()->getResultArray();

            // 2) joined rows for not-paid (legacy) within date range
            $qb = $db->table('billings')
                ->select('billings.id')
                ->join('users', 'users.id = billings.user_id', 'left')
                ->join('user_information ui', 'ui.user_id = billings.user_id', 'left')
                ->where('DATE(billings.updated_at) >=', $start)
                ->where('DATE(billings.updated_at) <=', $end)
                ->where('billings.status !=', 'Paid');

            $rows = $qb->get()->getResultArray();
            $totalRows = is_array($rows) ? count($rows) : 0;
            $distinctBills = $totalRows ? count(array_unique(array_column($rows, 'id'))) : 0;

            // 3) billing ids with multiple join matches (show counts)
            $dupQ = $db->table('billings')
                ->select('billings.id, billings.bill_no, COUNT(*) as cnt')
                ->join('user_information ui', 'ui.user_id = billings.user_id', 'left')
                ->where('DATE(billings.updated_at) >=', $start)
                ->where('DATE(billings.updated_at) <=', $end)
                ->where('billings.status !=', 'Paid')
                ->groupBy('billings.id, billings.bill_no')
                ->having('cnt > 1');

            $dups = $dupQ->get()->getResultArray();

            return $this->response->setJSON([
                'start' => $start,
                'end' => $end,
                'statusDistribution' => $dist,
                'joinedRows' => $totalRows,
                'distinctBillings' => $distinctBills,
                'duplicateIdRows' => $dups,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[Admin::partialBillingsDiagnostics] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

     // ---------------- Failed Transactions ----------------


    // Show failed & rejected transactions page
    public function failedTransactions()
    {
        $data = [
            'current_month' => date('Y-m-d')
        ];

        return view('admin/failed_transactions', $data);
    }

    public function getFailedPaymentsData()
{
    $request = service('request');

    $month = $request->getGet('month') ?? date('Y-m');
    $method = $request->getGet('method') ?? '';
    $search = $request->getGet('search') ?? '';
    $page = max(1, (int) $request->getGet('page'));
    $perPage = 20;
    $offset = ($page - 1) * $perPage;

    // Fetch payments via the model (existing method). We will filter to rejected/failed here.
    // If you prefer DB-side status filtering, see the optional PaymentsModel changes below.
    $filters = [
        'month'  => $month,
        'method' => $method ?: null,
        'search' => $search ?: null,
        // don't set limit/offset here so we can filter then paginate in PHP
    ];

    $allPayments = $this->paymentsModel->getMonthlyPayments($filters);

    // Filter: keep only rejected OR failed, and exclude offline/over-the-counter entries
    $filtered = array_filter($allPayments, function ($p) use ($method) {
        $status = strtolower($p['status'] ?? '');
        if (!in_array($status, ['rejected', 'failed'])) {
            return false;
        }
        // Exclude "offline" (over-the-counter) records for this view
        if (isset($p['method']) && strtolower($p['method']) === 'offline') {
            return false;
        }
        // If the client provided a method filter, respect it
        if (!empty($method) && $p['method'] !== $method) {
            return false;
        }
        return true;
    });

    // Pagination
    $totalItems = count($filtered);
    $paged = array_values(array_slice($filtered, $offset, $perPage));

    // Compute lightweight stats for this filtered set
    $totalUsers = count(array_unique(array_column($filtered, 'user_id')));
    $totalAmount = array_sum(array_map(function ($r) {
        return isset($r['amount']) ? (float)$r['amount'] : 0;
    }, $filtered));

    $gcashCount = 0;
    $gatewayCount = 0;
    foreach ($filtered as $r) {
        $m = strtolower($r['method'] ?? '');
        if ($m === 'manual') $gcashCount++;
        if ($m === 'gateway') $gatewayCount++;
    }

    $stats = [
        'total_users'  => $totalUsers,
        'total_amount' => $totalAmount,
        'gateway'      => $gatewayCount,
        'gcash'        => $gcashCount,
    ];

    // Response shape matches what the frontend expects
    return $this->response->setJSON([
        'success'    => true,
        'payments'   => $paged,
        'stats'      => $stats,
        'pagination' => [
            'total'        => $totalItems,
            'per_page'     => $perPage,
            'current_page' => $page,
        ],
    ]);
}

     // ---------------- Overdue Functions ----------------
   
    // Show overdue payments page
     public function overduePayments()
    {
        return view('admin/overdue_payments');
    }

    // Get overdue payments data (AJAX)
    public function getOverduePaymentsData()
    {
        $month = $this->request->getGet('month') ?? date('Y-m');
        $search = $this->request->getGet('search') ?? '';
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $billingModel = new \App\Models\BillingModel();

        // Build query for overdue bills
        // Use due_date-based detection so bills whose due_date has passed (and are not Paid)
        // will be considered overdue. This is more reliable than filtering by billing_month.
        $builder = $billingModel->db->table('billings b')
            ->select('b.*, u.email, ui.first_name, ui.last_name')
            ->join('users u', 'u.id = b.user_id', 'left')
            ->join('user_information ui', 'ui.user_id = u.id', 'left')
            ->where('DATE(b.due_date) <', date('Y-m-d'))
            ->where('b.status <>', 'Paid')
            ->where('b.paid_date IS NULL');

        if ($search !== '') {
            $builder->groupStart()
                ->like('ui.first_name', $search)
                ->orLike('ui.last_name', $search)
                ->orLike('u.email', $search)
                ->orLike('b.bill_no', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $rows = $builder->orderBy('b.billing_month', 'ASC')->limit($perPage, $offset)->get()->getResultArray();

        $payments = array_map(function($b) {
            return [
                'user_name' => trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')),
                'email' => $b['email'] ?? '',
                'amount_due' => $b['amount_due'],
                'billing_month' => $b['billing_month'],
                'due_date' => $b['due_date'],
                'status' => 'Overdue',
                'bill_no' => $b['bill_no'],
            ];
        }, $rows);

        $stats = [
            'total_users' => count(array_unique(array_column($rows, 'user_id'))),
            'total_amount' => array_sum(array_column($rows, 'amount_due')),
        ];

        return $this->response->setJSON([
            'payments' => $payments,
            'stats' => $stats,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
            ],
            'success' => true,
        ]);
    }


    // Activity logs now handled in SuperAdmin controller. Admin-side log endpoints removed.
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

// ===================== PROFILE MANAGEMENT =====================

    // Show admin profile
    public function editProfile()
    {
        $adminId = session()->get('admin_id');
        if (!$adminId) return redirect()->to(base_url('admin/login'))->with('error', 'You must be logged in.');

        $admin = $this->adminModel->find($adminId);
        if (!$admin) return redirect()->to(base_url('admin/login'))->with('error', 'Admin not found.');

        return view('admin/edit_profile', ['admin' => $admin]);
    }
    // Update admin profile
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

        $didUpdate = false;
        try {
            $didUpdate = $this->adminModel->update($adminId, $updateData);
        } catch (\Throwable $e) {
            // Log update errors for debugging
            if (function_exists('log_message')) log_message('error', 'updateProfile failed: ' . $e->getMessage());
            $didUpdate = false;
        }

        // Update session values on success
        if ($didUpdate) {
            session()->set([
                'admin_first_name'  => $updateData['first_name'],
                'admin_middle_name' => $updateData['middle_name'],
                'admin_last_name'   => $updateData['last_name'],
                'admin_email'       => $updateData['email'],
                'admin_picture'     => $updateData['profile_picture'] ?? $admin['profile_picture'] ?? 'default.png',
            ]);
        }

        // If this is an AJAX/fetch request, return JSON so the client can handle it.
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' || strpos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

        if ($isAjax) {
            if ($didUpdate) {
                return $this->response->setJSON(['success' => true, 'message' => 'Profile updated successfully.']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update profile.']);
        }

        if ($didUpdate) {
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

        // Clear any forced-change flags in DB and session so the admin can continue
        try {
            $this->adminModel->update($adminId, ['must_change_password' => 0]);
        } catch (\Throwable $_) { }
        try {
            session()->set('force_password_change', false);
            session()->remove('force_password_change');
        } catch (\Throwable $_) { }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Password changed successfully',
            // Direct SPA-driven clients to load the dashboard content fragment
            'redirect' => base_url('admin/')
        ]);
    }

}