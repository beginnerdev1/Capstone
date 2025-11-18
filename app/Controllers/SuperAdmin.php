<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AdminModel;




class SuperAdmin extends Controller
{
    public function index()
    {
        // Prepare display name for the sidebar (keep logic out of the view)
        $first = session()->get('superadmin_first_name') ?? '';
        $last  = session()->get('superadmin_last_name') ?? '';
        $email = session()->get('superadmin_email') ?? '';
        $sa_display = trim($first . ' ' . $last);
        if ($sa_display === '') {
            $sa_display = $email;
        }

        return view('superadmin/dashboard', ['sa_display' => $sa_display]);
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

    // Show the Create Super Admin page (AJAX-loaded)
    public function create_superadmin()
    {
        if (! session()->get('superadmin_id')) return redirect()->to(site_url('superadmin/login'));
        return view('superadmin/create_superadmin');
    }

    // List super admins (AJAX)
    public function getSuperAdmins()
    {
        $model = new \App\Models\SuperAdminModel();
        $rows = $model->orderBy('created_at','DESC')->findAll();
        return $this->response->setJSON($rows);
    }

    // Create a new super admin immediately (limit 2)
    public function createSuperAdmin()
    {
        $email = trim($this->request->getPost('email') ?? '');
        $firstName = trim($this->request->getPost('first_name') ?? '');
        $lastName = trim($this->request->getPost('last_name') ?? '');
        $password = $this->request->getPost('password') ?? '';
        $confirm = $this->request->getPost('confirm_password') ?? '';

        if (!$email || !$password || !$confirm || !$firstName) return $this->response->setJSON(['status'=>'error','message'=>'Missing fields (first name, email, password required)']);
        if ($password !== $confirm) return $this->response->setJSON(['status'=>'error','message'=>'Passwords do not match']);
        if (strlen($password) < 8) return $this->response->setJSON(['status'=>'error','message'=>'Password too short']);

        $model = new \App\Models\SuperAdminModel();
        $existing = $model->where('email', $email)->first();
        if ($existing) return $this->response->setJSON(['status'=>'error','message'=>'Email already used']);

        $all = $model->findAll();
        if (count($all) >= 2) return $this->response->setJSON(['status'=>'error','message'=>'Maximum of 2 super admins allowed']);

        $code = $model->generateAdminCode();
        // Determine is_primary: only set to primary if there is no existing primary
        $existingPrimary = $model->where('is_primary', 1)->first();
        $isPrimary = $existingPrimary ? 0 : 1;

        $data = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'admin_code' => $code,
            'is_primary' => $isPrimary,
        ];

        $insertId = $model->insert($data);
        if ($insertId) return $this->response->setJSON(['status'=>'success','message'=>'Super admin created']);
        return $this->response->setJSON(['status'=>'error','message'=>'Failed to create super admin']);
    }

    // Propose deletion of a super admin (creates a pending action)
    public function retireSuperAdmin()
    {
        $id = (int) ($this->request->getPost('id') ?? 0);
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);
        if (!$id) return $this->response->setJSON(['status'=>'error','message'=>'Missing target id']);
        $model = new \App\Models\SuperAdminModel();
        $target = $model->find($id);
        if (!$target) return $this->response->setJSON(['status'=>'error','message'=>'Target not found']);
        if (!empty($target['is_primary'])) return $this->response->setJSON(['status'=>'error','message'=>'Cannot delete primary super admin']);
        if ($id === $current) return $this->response->setJSON(['status'=>'error','message'=>'You cannot retire your own account']);

        $actionModel = new \App\Models\SuperAdminActionModel();
        $payload = ['target_id'=>$id];
        $aid = $actionModel->createAction('delete', $payload, $current);
        if ($aid) return $this->response->setJSON(['status'=>'pending','message'=>'Retire proposed, awaiting approval','action_id'=>$aid]);
        return $this->response->setJSON(['status'=>'error','message'=>'Failed to propose retire']);
    }

    // Return pending actions
    public function pendingActions()
    {
        $model = new \App\Models\SuperAdminActionModel();
        $rows = $model->getPending();
        return $this->response->setJSON($rows);
    }

    public function approveAction()
    {
        $actionId = (int) ($this->request->getPost('action_id') ?? 0);
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);
        if (!$actionId) return $this->response->setJSON(['status'=>'error','message'=>'Missing action id']);

        $actionModel = new \App\Models\SuperAdminActionModel();
        $act = $actionModel->find($actionId);
        if (!$act || $act['status'] !== 'pending') return $this->response->setJSON(['status'=>'error','message'=>'Action not found or not pending']);
        if ((int)$act['proposer_id'] === $current) return $this->response->setJSON(['status'=>'error','message'=>'You cannot approve your own action']);

        $payload = json_decode($act['payload'], true);
        if ($act['action_type'] === 'delete') {
            $targetId = (int) ($payload['target_id'] ?? 0);
            $model = new \App\Models\SuperAdminModel();
            $target = $model->find($targetId);
            if (!$target) return $this->response->setJSON(['status'=>'error','message'=>'Target not found']);
            if (!empty($target['is_primary'])) return $this->response->setJSON(['status'=>'error','message'=>'Cannot delete primary super admin']);
            $model->delete($targetId);
        }
        $actionModel->markApproved($actionId, $current);
        return $this->response->setJSON(['status'=>'success','message'=>'Action approved and executed']);
    }

    public function rejectAction()
    {
        $actionId = (int) ($this->request->getPost('action_id') ?? 0);
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);
        if (!$actionId) return $this->response->setJSON(['status'=>'error','message'=>'Missing action id']);
        $actionModel = new \App\Models\SuperAdminActionModel();
        $act = $actionModel->find($actionId);
        if (!$act || $act['status'] !== 'pending') return $this->response->setJSON(['status'=>'error','message'=>'Action not found or not pending']);
        if ((int)$act['proposer_id'] === $current) return $this->response->setJSON(['status'=>'error','message'=>'You cannot reject your own action']);
        $actionModel->markRejected($actionId, $current);
        return $this->response->setJSON(['status'=>'success','message'=>'Action rejected']);
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

    // Resolve actor display name (AJAX)
    public function getActorDisplay()
    {
        $actorType = trim($this->request->getGet('actor_type') ?? '');
        $actorId   = (int) ($this->request->getGet('actor_id') ?? 0);
        if (!$actorType || $actorId <= 0) return $this->response->setJSON(['status'=>'error','message'=>'Missing params']);

        if ($actorType === 'admin') {
            $m = new \App\Models\AdminModel();
            $row = $m->find($actorId);
            if ($row) {
                $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['username'] ?? $row['email'] ?? 'admin#'.$actorId);
                return $this->response->setJSON(['status'=>'success','display'=>$name]);
            }
        }

        if ($actorType === 'superadmin') {
            $m = new \App\Models\SuperAdminModel();
            $row = $m->find($actorId);
            if ($row) {
                $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['email'] ?? 'superadmin#'.$actorId);
                return $this->response->setJSON(['status'=>'success','display'=>$name]);
            }
        }

        return $this->response->setJSON(['status'=>'error','message'=>'Not found']);
    }

    // Batch resolve actor display names (POST JSON: {actors: [{type:'admin',id:1}, ...]})
    public function getActorDisplays()
    {
        $payload = $this->request->getJSON(true);
        $pairs = $payload['actors'] ?? [];
        $result = [];
        foreach ($pairs as $p) {
            $type = $p['type'] ?? '';
            $id = isset($p['id']) ? (int)$p['id'] : 0;
            $key = $type . '#' . $id;
            if ($id <= 0 || $type === '') { $result[$key] = $key; continue; }

            if ($type === 'admin') {
                $m = new \App\Models\AdminModel();
                $row = $m->find($id);
                if ($row) {
                    $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['username'] ?? $row['email'] ?? $key);
                    $result[$key] = $name; continue;
                }
            }

            if ($type === 'superadmin') {
                $m = new \App\Models\SuperAdminModel();
                $row = $m->find($id);
                if ($row) {
                    $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['email'] ?? $key);
                    $result[$key] = $name; continue;
                }
            }

            $result[$key] = $key;
        }

        return $this->response->setJSON(['status' => 'success', 'map' => $result]);
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
        // Resolve actor display names in batch to include human-friendly actor names in CSV
        $actorPairs = [];
        foreach ($rows as $r) {
            $t = $r['actor_type'] ?? '';
            $id = isset($r['actor_id']) ? (int)$r['actor_id'] : 0;
            if ($t !== '' && $id > 0) {
                $actorPairs[$t . '#' . $id] = ['type' => $t, 'id' => $id];
            }
        }
        $actorMap = [];
        if (!empty($actorPairs)) {
            foreach ($actorPairs as $key => $p) {
                if ($p['type'] === 'admin') {
                    $m = new \App\Models\AdminModel();
                    $row = $m->find($p['id']);
                    if ($row) $actorMap[$key] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['username'] ?? $row['email'] ?? $key);
                }
                if ($p['type'] === 'superadmin') {
                    $m = new \App\Models\SuperAdminModel();
                    $row = $m->find($p['id']);
                    if ($row) $actorMap[$key] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['email'] ?? $key);
                }
                if (!isset($actorMap[$key])) $actorMap[$key] = $key;
            }
        }

        $fh = fopen('php://temp', 'w+');
        // Include actor type/id and details for complete logs
        fputcsv($fh, ['Time','Actor','Actor Type','Actor ID','Action','Method','Route','Resource','Details','IP','User Agent','Logged Out']);
        foreach ($rows as $r) {
            $actorKey = ($r['actor_type'] ?? '') . '#' . ($r['actor_id'] ?? '');
            $actorDisplay = $actorMap[$actorKey] ?? $actorKey;

            // Prefer top-level resource, but fall back to the most-recent action's resource
            $resourceVal = '';
            if (!empty($r['resource'])) {
                $resourceVal = $r['resource'];
            } elseif (!empty($r['details'])) {
                $decoded = json_decode($r['details'], true);
                if (is_array($decoded) && count($decoded) > 0) {
                    // details may be an array of action entries (we stored actions there)
                    $last = end($decoded);
                    if (is_array($last) && isset($last['resource'])) {
                        $resourceVal = $last['resource'];
                    }
                }
            }

            fputcsv($fh, [
                (!empty($r['created_at']) && $r['created_at'] !== '0000-00-00 00:00:00') ? date('m/d/Y H:i:s', strtotime($r['created_at'])) : '',
                $actorDisplay,
                $r['actor_type'] ?? '',
                $r['actor_id'] ?? '',
                $r['action'] ?? '',
                $r['method'] ?? '',
                $r['route'] ?? '',
                $resourceVal,
                $r['details'] ?? '',
                $r['ip_address'] ?? '',
                $r['user_agent'] ?? '',
                (!empty($r['logged_out_at']) && $r['logged_out_at'] !== '0000-00-00 00:00:00') ? date('m/d/Y H:i:s', strtotime($r['logged_out_at'])) : '',
            ]);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="system-activity-logs.csv"')
            ->setHeader('Content-Length', (string) strlen($csv))
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

    // Show profile edit form for current superadmin
    public function profile()
    {
        $id = session()->get('superadmin_id');
        if (! $id) return redirect()->to(site_url('superadmin/login'));
        $model = new \App\Models\SuperAdminModel();
        $row = $model->find($id);
        return view('superadmin/profile', ['row' => $row]);
    }

    // Update profile (AJAX)
    public function updateProfile()
    {
        $id = session()->get('superadmin_id');
        if (! $id) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);

        $firstName  = trim($this->request->getPost('first_name') ?? '');
        $middleName = trim($this->request->getPost('middle_name') ?? '');
        $lastName   = trim($this->request->getPost('last_name') ?? '');
        $email      = trim($this->request->getPost('email') ?? '');
        $password   = $this->request->getPost('password') ?? '';
        $confirm    = $this->request->getPost('confirm_password') ?? '';

        if (!$firstName || !$lastName || !$email) {
            return $this->response->setJSON(['status'=>'error','message'=>'First name, last name and email are required']);
        }

        $model = new \App\Models\SuperAdminModel();
        $existing = $model->where('email', $email)->first();
        if ($existing && (int)$existing['id'] !== (int)$id) {
            return $this->response->setJSON(['status'=>'error','message'=>'Email already in use']);
        }

        // Only include columns that actually exist in the table to avoid DB errors
        $db = \Config\Database::connect();
        try {
            $columns = $db->getFieldNames('super_admin');
        } catch (\Exception $e) {
            $columns = [];
        }

        $data = [];
        if (in_array('first_name', $columns))  $data['first_name']  = $firstName;
        if (in_array('middle_name', $columns)) $data['middle_name'] = $middleName;
        if (in_array('last_name', $columns))   $data['last_name']   = $lastName;
        if (in_array('email', $columns))       $data['email']       = $email;

        if ($password !== '') {
            if ($password !== $confirm) return $this->response->setJSON(['status'=>'error','message'=>'Passwords do not match']);
            if (strlen($password) < 8) return $this->response->setJSON(['status'=>'error','message'=>'Password too short']);
            if (in_array('password', $columns)) $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (empty($data)) {
            return $this->response->setJSON(['status'=>'error','message'=>'No updatable fields found (run migrations).']);
        }

        $ok = $model->update($id, $data);
        if ($ok) {
            // Update session values if present
            if (in_array('email', $columns)) session()->set('superadmin_email', $email);
            if (in_array('first_name', $columns)) session()->set('superadmin_first_name', $firstName);
            if (in_array('middle_name', $columns)) session()->set('superadmin_middle_name', $middleName);
            if (in_array('last_name', $columns)) session()->set('superadmin_last_name', $lastName);
            return $this->response->setJSON(['status'=>'success','message'=>'Profile updated']);
        }
        return $this->response->setJSON(['status'=>'error','message'=>'Failed to update profile']);
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
