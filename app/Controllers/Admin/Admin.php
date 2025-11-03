<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use App\Models\UserInformationModel;

class Admin extends BaseController
{
    protected $usersModel;
    protected $userInfoModel;
    protected $billingModel;
    protected $adminModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->userInfoModel = new UserInformationModel();
        $this->billingModel = new BillingModel();
        $this->adminModel = new AdminModel();
    }

    public function index()
    {
        $billingModel = new \App\Models\BillingModel();

        // Get monthly totals
        $query = $billingModel->select("
                DATE_FORMAT(updated_at, '%b') AS month,
                SUM(amount_due) AS total
            ")
            ->where('status', 'Paid')
            ->groupBy('month')
            ->orderBy('updated_at', 'ASC')
            ->get();

        $months = [];
        $incomeData = [];

        foreach ($query->getResultArray() as $row) {
            $months[] = $row['month'];
            $incomeData[] = (float) $row['total'];
        }

        $data = [
            'title' => 'Dashboard',
            'months' => $months,
            'incomeData' => $incomeData,
        ];

        // ✅ Render the page (which extends the layout)
        return view('admin/index', $data);
    }
    public function layoutStatic()
    {
        return view('admin/layout-static');
    } 
    public function charts()
    {
        return view('admin/charts');
    }
    public function page404()
    {
        return view('admin/404');
    }
    public function page401()
    {
        return view('admin/401');
    }
    public function page500()
    {
        return view('admin/500');
    }
    public function tables()
    {
        return view('admin/tables');
    }

    public function registeredUsers()
    {
        $usersModel = new \App\Models\UsersModel();
        $userInfoModel = new \App\Models\UserInformationModel();

        $puroks = ['1', '2', '3', '4', '5'];
        $selectedPurok = $this->request->getGet('purok');
        $search = $this->request->getGet('search');

        // Build query with STATUS included
        $builder = $usersModel
            ->select('
                users.id,
                users.status,
                user_information.first_name,
                user_information.last_name,
                user_information.email,
                user_information.barangay,
                user_information.municipality,
                user_information.purok,
                users.active
            ')
            ->join('user_information', 'user_information.user_id = users.id', 'left');

        // Filter by Purok
        if ($selectedPurok) {
            $builder->where('user_information.purok', $selectedPurok);
        }

        // Search by name or email
        if ($search) {
            $builder->groupStart()
                    ->like('user_information.first_name', $search)
                    ->orLike('user_information.last_name', $search)
                    ->orLike('user_information.email', $search)
                ->groupEnd();
        }

        // Pagination: 10 users per page
        $users = $builder->paginate(10);
        $pager = $usersModel->pager;

        $data = [
            'title' => 'Registered Users',
            'users' => $users,
            'pager' => $pager,
            'puroks' => $puroks,
            'selectedPurok' => $selectedPurok,
            'search' => $search
        ];

        return view('admin/registeredUsers', $data);
    }

    public function pendingAccounts()
    {
        // Fetch users whose status is "Pending"
        $users = $this->usersModel
            ->select('users.id, users.email, users.status, user_information.first_name, user_information.last_name, user_information.purok, user_information.barangay')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->where('users.status', 'Pending')
            ->findAll();

        return view('admin/pendingAccounts', ['users' => $users]);
    }

   public function approve($id)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'No user ID provided.');
        }

        $updateData = ['status' => 'approved'];

        if ($this->usersModel->update($id, $updateData)) {
            return redirect()->back()->with('success', 'User approved successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve user.');
    }

    public function reject($id)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'No user ID provided.');
        }

        $updateData = ['status' => 'rejected'];

        if ($this->usersModel->update($id, $updateData)) {
            return redirect()->back()->with('success', 'User rejected successfully.');
        }

        return redirect()->back()->with('error', 'Failed to reject user.');
    }

   public function viewUser($id)
    {
        $usersModel = new \App\Models\UsersModel();
        $userInfoModel = new \App\Models\UserInformationModel();

        $user = $usersModel
            ->select('users.*, user_information.*')
            ->join('user_information', 'user_information.user_id = users.id', 'left')
            ->where('users.id', $id)
            ->first();

        if (!$user) {
            return redirect()->to('/admin/registeredUsers')->with('error', 'User not found');
        }

        $data = [
            'title' => 'View User Details',
            'user'  => $user
        ];

        return view('admin/viewUser', $data);
    }

    // Toggle active/inactive
   public function toggleUserStatus($id)
    {
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $newStatus = $user['active'] ? 0 : 1;
        $usersModel->update($id, ['active' => $newStatus]);

        return redirect()->back()->with('success', 'User status updated.');
    }

     public function getUserInfo()
    {
        $userId = $this->request->getGet('user_id'); 
        $username = $this->request->getGet('username');

        $userModel = new \App\Models\UserModel();

        // You can search by either ID or username
        $user = null;
        if (!empty($userId)) {
            $user = $userModel->where('id', $userId)->first();
        } elseif (!empty($username)) {
            $user = $userModel->where('username', $username)->first();
        }

        if ($user) {
            // Join with user_information if needed
            $userInfoModel = new \App\Models\UserInformationModel();
            $info = $userInfoModel->where('user_id', $user['id'])->first();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'email' => $user['email'] ?? ($info['email'] ?? ''),
                    'address' => $info['address'] ?? '',
                    'username' => $user['username'],
                    'id' => $user['id']
                ]
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
    }

     public function manageAccounts()
    {
        $status = $this->request->getGet('status') ?? '';
        $search = $this->request->getGet('search') ?? '';

        // Start with all users
        $users = $this->usersModel->select('users.id, users.email, users.is_verified, user_information.first_name, user_information.last_name, user_information.phone, user_information.barangay, user_information.purok')
            ->join('user_information', 'user_information.user_id = users.id', 'left');

        if (!empty($search)) {
            $users->groupStart()
                ->like('user_information.first_name', $search)
                ->orLike('user_information.last_name', $search)
                ->groupEnd();
        }

        $users = $users->findAll();

        // Attach bills per user
        foreach ($users as &$user) {
            $builder = $this->billingModel->where('user_id', $user['id']);

            // Filter by selected status
            if (!empty($status) && strtolower($status) != 'all') {
                if ($status == 'Paid') {
                    // Include both Paid and Over the Counter
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

        // Filter out users with no bills if a specific status is selected
        if (!empty($status) && strtolower($status) != 'all') {
            $users = array_filter($users, fn($u) => !empty($u['unpaid_bills']));
        }

        return view('admin/manageAccounts', [
            'users' => $users,
            'search' => $search,
            'selectedStatus' => $status,
            'payment' => $this->adminModel->first(),
        ]);
    }

    public function markAsPaid($billId)
    {
        // Find the bill
        $bill = $this->billingModel->find($billId);

        if (!$bill) {
            return redirect()->back()->with('error', 'Bill not found.');
        }

        // Update status to Paid
        $this->billingModel->update($billId, [
            'status' => 'Paid',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Bill has been marked as Paid.');
    }


   public function announcements()
    {
        $data['title'] = 'Announcements';
        return view('admin/announcements', $data);
    }
  
    public function reports()
    {
        $data = [
            'title' => 'Reports',
        ];

        return view('admin/layouts/main', [
            'title' => 'Reports',
            'content' => view('admin/Reports', $data)
        ]);
    }
    
    public function paymentSettings()
    {
        $paymentModel = new PaymentSettingsModel();

        $data = [
            'title' => 'Payment Settings',
            'payment' => $paymentModel->first(), // get the only record (we’ll keep just 1 row)
        ];

        return view('admin/paymentSettings', $data);
    }

    public function updateQR()
    {
        $paymentModel = new PaymentSettingsModel();

        $file = $this->request->getFile('qr_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/qr/', $newName);

            $data = [
                'payment_method' => $this->request->getPost('payment_method'),
                'account_name' => $this->request->getPost('account_name'),
                'account_number' => $this->request->getPost('account_number'),
                'qr_image' => 'uploads/qr/' . $newName,
            ];

            $paymentModel->update(1, $data); // assuming only one record
            return redirect()->to('admin/manageAccounts')->with('success', 'QR updated successfully!');
        }

        return redirect()->back()->with('error', 'QR upload failed!');
    }
    //view profile
    public function profile()
    {
        $adminId = session()->get('admin_id');

        if (!$adminId) {
            return redirect()->to(base_url('admin/login'))->with('error', 'You must be logged in.');
        }

        $admin = $this->adminModel->find($adminId);

        if (!$admin) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Admin not found.');
        }

        return view('admin/profile', ['admin' => $admin]);
    }
    //update profile
    public function updateProfile()
    {
        helper(['form', 'url']);

        $adminId = session()->get('admin_id');
        if (!$adminId) {
            return redirect()->to(base_url('admin/login'))->with('error', 'You must be logged in.');
        }

        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            return redirect()->back()->with('error', 'Admin not found.');
        }

        $updateData = [
            'first_name'  => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name'   => $this->request->getPost('last_name'),
            'email'       => $this->request->getPost('email'),
        ];

        // ✅ Handle file upload
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($file->move($uploadPath, $newName)) {
                // Delete old file if exists
                if (!empty($admin['profile_picture']) && $admin['profile_picture'] !== 'default.png') {
                    $oldFile = $uploadPath . $admin['profile_picture'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $updateData['profile_picture'] = $newName;
            }
        }

        // ✅ Save update
        $this->adminModel->update($adminId, $updateData);

        // ✅ Update session
        session()->set([
            'admin_first_name'  => $updateData['first_name'],
            'admin_middle_name' => $updateData['middle_name'],
            'admin_last_name'   => $updateData['last_name'],
            'admin_email'       => $updateData['email'],
            'admin_picture'     => $updateData['profile_picture'] ?? $admin['profile_picture'] ?? 'default.png',
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
