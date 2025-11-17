<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AdminModel;




class SuperAdmin extends Controller
{
    public function index()
    {
        // Render AJAX-enabled layout wrapper
        return view('superadmin/Dashboard');
    }
    
    public function loginForm()
    {
        return view('superadmin/login');
    }

    public function checkCodeForm()
    {
        return view('superadmin/check_code');
    }


    public function dashboard()
    {
        // Backwards-compat route; show content only
        return view('superadmin/dashboard-content');
    }

    // AJAX content for the main dashboard area
    public function content()
    {
        return view('superadmin/dashboard-content');
    }

    public function users()
    {
        return view('superadmin/users');
    }

    public function logs()
    {
        return view('superadmin/logs');
    }

    public function getLogs()
    {
        $model = new \App\Models\AdminActivityLogModel();
        $builder = $model;
        $limit = (int) ($this->request->getGet('limit') ?? 500);
        $limit = max(1, min(2000, $limit));
        $actorType = trim($this->request->getGet('actor_type') ?? '');
        $actorId = (int) ($this->request->getGet('actor_id') ?? 0);
        $action = trim($this->request->getGet('action') ?? '');
        $method = trim($this->request->getGet('method') ?? '');
        $q = trim($this->request->getGet('q') ?? '');
        $start = trim($this->request->getGet('start') ?? '');
        $end = trim($this->request->getGet('end') ?? '');
        if ($actorType !== '') { $builder->where('actor_type', $actorType); }
        if ($actorId > 0) { $builder->where('actor_id', $actorId); }
        if ($action !== '') { $builder->where('action', $action); }
        if ($method !== '') { $builder->where('method', strtoupper($method)); }
        if ($start !== '') { $builder->where('created_at >=', $start . ' 00:00:00'); }
        if ($end !== '') { $builder->where('created_at <=', $end . ' 23:59:59'); }
        if ($q !== '') {
            $builder->groupStart()->like('route', $q)->orLike('resource', $q)->orLike('details', $q)->groupEnd();
        }
        $rows = $builder->orderBy('id','DESC')->findAll($limit);
        return $this->response->setJSON($rows);
    }

    public function exportLogs()
    {
        $model = new \App\Models\AdminActivityLogModel();
        $builder = $model;
        $actorType = trim($this->request->getGet('actor_type') ?? '');
        $actorId = (int) ($this->request->getGet('actor_id') ?? 0);
        $action = trim($this->request->getGet('action') ?? '');
        $method = trim($this->request->getGet('method') ?? '');
        $q = trim($this->request->getGet('q') ?? '');
        $start = trim($this->request->getGet('start') ?? '');
        $end = trim($this->request->getGet('end') ?? '');
        if ($actorType !== '') { $builder->where('actor_type', $actorType); }
        if ($actorId > 0) { $builder->where('actor_id', $actorId); }
        if ($action !== '') { $builder->where('action', $action); }
        if ($method !== '') { $builder->where('method', strtoupper($method)); }
        if ($start !== '') { $builder->where('created_at >=', $start . ' 00:00:00'); }
        if ($end !== '') { $builder->where('created_at <=', $end . ' 23:59:59'); }
        if ($q !== '') { $builder->groupStart()->like('route', $q)->orLike('resource', $q)->orLike('details', $q)->groupEnd(); }
        $rows = $builder->orderBy('id','DESC')->findAll(5000);
        $fh = fopen('php://temp', 'w+');
        fputcsv($fh, ['Time','Actor','Action','Method','Route','Resource','IP','User Agent','Logged Out']);
        foreach ($rows as $r) {
            fputcsv($fh, [
                $r['created_at'] ?? '',
                ($r['actor_type'] ?? '') . '#' . ($r['actor_id'] ?? ''),
                $r['action'] ?? '',
                $r['method'] ?? '',
                $r['route'] ?? '',
                $r['resource'] ?? '',
                $r['ip_address'] ?? '',
                $r['user_agent'] ?? '',
                $r['logged_out_at'] ?? '',
            ]);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="system-activity-logs.csv"')
            ->setBody($csv);
    }

    // Handle user creation AJAX
    public function createUser()
    {
        $adminModel = new \App\Models\AdminModel();
        $archivedModel = new \App\Models\AdminArchivedModel(); // ensure class exists

        $firstName  = trim($this->request->getPost('first_name') ?? '');
        $middleName = trim($this->request->getPost('middle_name') ?? '');
        $lastName   = trim($this->request->getPost('last_name') ?? '');
        $email      = trim($this->request->getPost('email') ?? '');
        $username   = trim($this->request->getPost('username') ?? '');
        $position   = trim($this->request->getPost('position') ?? '');
        $password   = $this->request->getPost('password') ?? '';
        $confirm    = $this->request->getPost('confirm_password') ?? '';

        // Basic required checks
        if (!$firstName || !$middleName || !$lastName || !$email || !$username || !$position || !$password || !$confirm) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
        }

        // Password confirmation
        if ($password !== $confirm) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Passwords do not match.'
            ]);
        }

        // Password policy (simple)
        if (strlen($password) < 8) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password must be at least 8 characters.'
            ]);
        }

        // Uniqueness checks for email, username
        if ($adminModel->where('email', $email)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email is already in use.'
            ]);
        }
        if ($adminModel->where('username', $username)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Username is already in use.'
            ]);
        }

        // Enforce unique active position (only one active occupant)
        $existingPosition = $adminModel->where('position', $position)->first();
        if ($existingPosition) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Position "' . $position . '" already occupied. Retire current holder before assigning a new one.'
            ]);
        }

        // Optional profile picture
        $profilePath = null;
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mime = $file->getMimeType();
            if (!in_array($mime, ['image/jpeg','image/png','image/gif'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Profile picture must be an image (jpeg, png, gif).'
                ]);
            }
            $targetDir = WRITEPATH . 'uploads/admin_profiles';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0775, true);
            }
            $newName = $file->getRandomName();
            if ($file->move($targetDir, $newName)) {
                $profilePath = 'uploads/admin_profiles/' . $newName; // relative to writable
            }
        }

        $data = [
            'first_name'      => $firstName,
            'middle_name'     => $middleName,
            'last_name'       => $lastName,
            'email'           => $email,
            'username'        => $username,
            'position'        => $position,
            'password'        => password_hash($password, PASSWORD_DEFAULT),
            'is_verified'     => 0,
            'profile_picture' => $profilePath,
        ];

        if ($adminModel->insert($data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Admin account created successfully.'
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to create admin.'
        ]);
    }
    

    // Fetch users with pagination AJAX
    public function getUsers()
    {
        $model= new AdminModel();
        $users = $model->orderBy('created_at', 'DESC')->findAll();

        return $this->response->setJSON($users);
    }

    public function settings()
    {
        return view('superadmin/settings');
    }

    // Retire (archive) an admin
    public function retireUser()
    {
        $id = (int) ($this->request->getPost('id') ?? 0);
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid admin id']);
        }
        $adminModel = new AdminModel();
        $row = $adminModel->find($id);
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Admin not found']);
        }

        $archiveModel = new \App\Models\AdminArchivedModel();
        $archiveData = [
            'original_admin_id' => $row['id'],
            'first_name'        => $row['first_name'] ?? null,
            'middle_name'       => $row['middle_name'] ?? null,
            'last_name'         => $row['last_name'] ?? null,
            'username'          => $row['username'] ?? null,
            'email'             => $row['email'] ?? null,
            'password'          => $row['password'] ?? null,
            'position'          => $row['position'] ?? null,
            'is_verified'       => $row['is_verified'] ?? 0,
            'profile_picture'   => $row['profile_picture'] ?? null,
            'created_at'        => $row['created_at'] ?? null,
            'updated_at'        => $row['updated_at'] ?? null,
            'archived_at'       => date('Y-m-d H:i:s'),
        ];

        $archiveModel->insert($archiveData);
        $adminModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Admin retired and archived.']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/superadmin/login');
    }

}
