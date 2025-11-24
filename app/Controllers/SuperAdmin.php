<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AdminModel;
use App\Models\SuperAdminModel;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;




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

    // Note: Interactive creation of additional superadmins has been removed.
    // This application enforces a single superadmin. Account creation via
    // controller/API is intentionally disabled to prevent multiple superadmins.

    // Return pending actions
    public function pendingActions()
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('superadmin_actions')) {
            // Table removed; return empty list so UI degrades gracefully
            return $this->response->setJSON([]);
        }
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
        $db = \Config\Database::connect();
        if (! $db->tableExists('superadmin_actions')) {
            return $this->response->setJSON(['status'=>'error','message'=>'Superadmin actions feature not available']);
        }
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

            // Notify the deleted superadmin (best-effort)
            try {
                $targetEmail = $target['email'] ?? '';
                if (!empty($targetEmail)) {
                    $targetName = trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? '')) ?: $targetEmail;
                    $actorName = '';
                    $currentRow = (new \App\Models\SuperAdminModel())->find($current);
                    if (!empty($currentRow)) {
                        $actorName = trim(($currentRow['first_name'] ?? '') . ' ' . ($currentRow['last_name'] ?? '')) ?: ($currentRow['email'] ?? 'Super Admin');
                    }
                    $time = date('Y-m-d H:i:s');
                    $html = view('emails/superadmin_removed', [
                        'targetName' => $targetName,
                        'actorName'  => $actorName,
                        'targetEmail'=> $targetEmail,
                        'time'       => $time,
                    ]);
                    $this->sendEmail($targetEmail, $targetName, 'Your Super Admin Account Has Been Removed', $html);
                }
            } catch (\Exception $e) {
                // ignore email failures
            }
        }
        $actionModel->markApproved($actionId, $current);

        // Send confirmation to the approver
        try {
            $approver = (new \App\Models\SuperAdminModel())->find($current);
            if (!empty($approver) && !empty($approver['email'])) {
                $approverEmail = $approver['email'];
                $approverName  = trim(($approver['first_name'] ?? '') . ' ' . ($approver['last_name'] ?? '')) ?: $approverEmail;
                $targetName = isset($targetName) ? $targetName : (trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? '')) ?: ($target['email'] ?? ''));
                $time = date('Y-m-d H:i:s');
                $html = view('emails/action_confirmation', [
                    'recipientName' => $approverName,
                    'actionLabel'   => 'Approved SuperAdmin Deletion',
                    'targetName'    => $targetName,
                    'targetEmail'   => $target['email'] ?? '',
                    'time'          => $time,
                    'actionId'      => $actionId,
                    'phase'         => 'approved',
                ]);
                $this->sendEmail($approverEmail, $approverName, 'SuperAdmin Deletion Approved', $html);
            }
        } catch (\Exception $_) { }

        return $this->response->setJSON(['status'=>'success','message'=>'Action approved and executed']);
    }

    public function rejectAction()
    {
        $actionId = (int) ($this->request->getPost('action_id') ?? 0);
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);
        if (!$actionId) return $this->response->setJSON(['status'=>'error','message'=>'Missing action id']);
        $db = \Config\Database::connect();
        if (! $db->tableExists('superadmin_actions')) {
            return $this->response->setJSON(['status'=>'error','message'=>'Superadmin actions feature not available']);
        }
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

    /**
     * Upload a backup ZIP for restore. Stores under WRITEPATH/backups/restores/ and
     * returns a JSON list of tables found in the ZIP (from JSON filenames).
     */
    public function uploadRestore()
    {
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);

        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status'=>'error','message'=>'Invalid request method']);
        }

        $file = $this->request->getFile('restore_zip');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status'=>'error','message'=>'No file uploaded or file invalid']);
        }
        $ext = strtolower(pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            return $this->response->setJSON(['status'=>'error','message'=>'Only ZIP archives are accepted']);
        }

        $destDir = WRITEPATH . 'backups/restores/';
        if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
        $filename = 'restore_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.zip';
        $target = $destDir . $filename;

        try {
            $file->move($destDir, $filename);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status'=>'error','message'=>'Failed to save uploaded file: ' . $e->getMessage()]);
        }

        // Inspect ZIP for JSON files and metadata
        $tables = [];
        $zip = new \ZipArchive();
        if ($zip->open($target) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'] ?? '';
                if (preg_match('/^(.+)\.json$/i', $name, $m)) {
                    $tbl = $m[1];
                    // skip generic metadata file
                    if (strtolower($tbl) === 'metadata') continue;
                    $tables[] = $tbl;
                }
            }
            $zip->close();
        }

        return $this->response->setJSON(['status'=>'success','message'=>'Uploaded','filename'=>$filename,'tables'=>$tables]);
    }

    /**
     * Apply a previously uploaded restore ZIP. Requires POST param `filename` and
     * `confirm` equal to 'RESTORE'. This performs a best-effort REPLACE INTO for each
     * JSON file named <table>.json contained in the archive. All work is executed
     * inside a DB transaction; on error the transaction is rolled back.
     */
    public function applyRestore()
    {
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status'=>'error','message'=>'Not authenticated']);
        if (!$this->request->is('post')) return $this->response->setJSON(['status'=>'error','message'=>'Invalid request method']);

        $filename = trim((string)$this->request->getPost('filename'));
        $confirm = trim((string)$this->request->getPost('confirm'));
        if ($confirm !== 'RESTORE') return $this->response->setJSON(['status'=>'error','message'=>'Restore not confirmed']);
        if ($filename === '') return $this->response->setJSON(['status'=>'error','message'=>'Missing filename']);

        $filePath = WRITEPATH . 'backups/restores/' . $filename;
        if (!file_exists($filePath)) return $this->response->setJSON(['status'=>'error','message'=>'Restore file not found']);

        $tmpDir = WRITEPATH . 'backups/tmp_restore_' . bin2hex(random_bytes(6)) . '/';
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) return $this->response->setJSON(['status'=>'error','message'=>'Failed to open ZIP archive']);

        // Extract only JSON files into tmp dir
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $name = $stat['name'] ?? '';
            if (preg_match('/^(.+)\.json$/i', $name)) {
                // extract to tmpDir preserving filename
                copy('zip://' . $filePath . '#' . $name, $tmpDir . basename($name));
            }
        }
        $zip->close();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // For each JSON file, perform REPLACE INTO for each row
            $files = glob($tmpDir . '*.json');
            $skippedTables = [];
            $processedTables = [];
            $errors = [];
            $rowIssues = [];

            foreach ($files as $f) {
                $base = basename($f);
                if (strtolower($base) === 'metadata.json') continue;
                $table = preg_replace('/\.json$/i', '', $base);

                // Skip if table does not exist
                if (! $db->tableExists($table)) {
                    $skippedTables[] = $table;
                    continue;
                }

                // Get column list for the table
                $tableCols = [];
                try {
                    $colResults = $db->query("SHOW COLUMNS FROM `{$table}`")->getResultArray();
                    foreach ($colResults as $cr) {
                        if (isset($cr['Field'])) $tableCols[] = $cr['Field'];
                    }
                } catch (\Throwable $_) {
                    // If we cannot get columns, skip table
                    $skippedTables[] = $table;
                    continue;
                }

                $contents = file_get_contents($f);
                $rows = json_decode($contents, true);
                if (!is_array($rows) || empty($rows)) continue;

                $processedTables[] = $table;

                foreach ($rows as $idx => $row) {
                    if (!is_array($row)) { $rowIssues[] = [ 'table' => $table, 'row' => $idx, 'reason' => 'not_array' ]; continue; }

                    // Filter row keys to table columns to avoid SQL errors
                    $filtered = array_intersect_key($row, array_flip($tableCols));
                    if (empty($filtered)) { $rowIssues[] = [ 'table' => $table, 'row' => $idx, 'reason' => 'no_matching_columns' ]; continue; }

                    $cols = array_keys($filtered);
                    $colList = implode(',', array_map(function($c){ return '`'.str_replace('`','',$c).'`'; }, $cols));
                    $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
                    $sql = "REPLACE INTO `{$table}` ({$colList}) VALUES ({$placeholders})";
                    $bindings = array_values($filtered);

                    try {
                        $db->query($sql, $bindings);
                        $err = $db->error();
                        if (!empty($err) && isset($err['code']) && $err['code'] != 0) {
                            $errors[] = [ 'table' => $table, 'row' => $idx, 'error' => $err ];
                        }
                    } catch (\Throwable $qe) {
                        $errors[] = [ 'table' => $table, 'row' => $idx, 'exception' => $qe->getMessage() ];
                    }
                }
            }

            // If any errors occurred, rollback and report summary
            if (!empty($errors)) {
                $db->transRollback();
                // cleanup tmp dir
                foreach (glob($tmpDir . '*') as $tf) @unlink($tf);
                @rmdir($tmpDir);
                log_message('error', 'applyRestore errors: ' . json_encode($errors));
                return $this->response->setJSON(['status'=>'error','message'=>'Database transaction failed during restore','debug'=>['errors'=>$errors,'skipped'=>$skippedTables,'row_issues'=>$rowIssues]]);
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                // cleanup tmp dir
                foreach (glob($tmpDir . '*') as $tf) @unlink($tf);
                @rmdir($tmpDir);
                return $this->response->setJSON(['status'=>'error','message'=>'Database transaction failed during restore','debug'=>['skipped'=>$skippedTables,'row_issues'=>$rowIssues]]);
            }

            // cleanup tmp dir
            foreach (glob($tmpDir . '*') as $tf) @unlink($tf);
            @rmdir($tmpDir);

            return $this->response->setJSON(['status'=>'success','message'=>'Restore applied','summary'=>['processed'=>$processedTables,'skipped'=>$skippedTables,'row_issues'=>$rowIssues]]);
        } catch (\Throwable $e) {
            $db->transRollback();
            // cleanup tmp dir
            foreach (glob($tmpDir . '*') as $tf) @unlink($tf);
            @rmdir($tmpDir);
            log_message('error', 'applyRestore error: ' . $e->getMessage());
            return $this->response->setJSON(['status'=>'error','message'=>'Restore failed: ' . $e->getMessage()]);
        }
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

    // Dashboard metrics (AJAX)
    public function dashboardMetrics()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $adminModel = new \App\Models\AdminModel();
        $superModel = new \App\Models\SuperAdminModel();
        $logModel = new \App\Models\AdminActivityLogModel();
        $actionModel = new \App\Models\SuperAdminActionModel();

        try {
            $admins = (int) $adminModel->countAll();
        } catch (\Throwable $_) { $admins = 0; }
        try { $superadmins = (int) $superModel->countAll(); } catch (\Throwable $_) { $superadmins = 0; }
        try { $logs = (int) $logModel->countAllResults(); } catch (\Throwable $_) { $logs = 0; }
        try { $pending = (int) $actionModel->where('status','pending')->countAllResults(); } catch (\Throwable $_) { $pending = 0; }

        // Active admins: distinct admin actor_ids with activity in the last N minutes
        try {
            $minutes = 10; // consider "active now" as activity within the last 10 minutes
            $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
            $db = \Config\Database::connect();
            $activeAdmins = (int) $db->table('admin_activity_logs')
                ->select('actor_id')
                ->where('actor_type', 'admin')
                ->where('created_at >=', $since)
                ->groupBy('actor_id')
                ->countAllResults();
        } catch (\Throwable $_) { $activeAdmins = 0; }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'admin_count' => $admins,
                'superadmin_count' => $superadmins,
                'log_count' => $logs,
                'pending_actions' => $pending,
                'active_admin_count' => $activeAdmins,
            ]
        ]);
    }

    // System info and backups (AJAX)
    public function systemInfo()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $db = \Config\Database::connect();
        $info = [];
        try {
            $info['php_version'] = PHP_VERSION;
            $info['os'] = PHP_OS_FAMILY . ' ' . PHP_OS;
            $info['db_version'] = $db->getVersion();
            // DB size for current database (best-effort)
            $query = $db->query("SELECT IFNULL(SUM(data_length+index_length),0) AS bytes FROM information_schema.tables WHERE table_schema = DATABASE()");
            $row = $query->getRowArray();
            $info['db_bytes'] = isset($row['bytes']) ? (int)$row['bytes'] : 0;
        } catch (\Throwable $_) {
            $info['db_version'] = 'unknown';
            $info['db_bytes'] = 0;
        }

        // Disk space and backups
        $writable = WRITEPATH . 'backups';
        $info['backups_dir'] = $writable;
        $info['disk_free'] = @disk_free_space(WRITEPATH) ?: 0;
        $info['disk_total'] = @disk_total_space(WRITEPATH) ?: 0;
        $backups = [];
        if (is_dir($writable)) {
            foreach (glob($writable . DIRECTORY_SEPARATOR . '*') as $f) {
                if (!is_file($f)) continue;
                $backups[] = [
                    'name' => basename($f),
                    'size' => filesize($f),
                    'mtime' => date('c', filemtime($f)),
                ];
            }
            usort($backups, function($a,$b){ return strcmp($b['mtime'],$a['mtime']); });
        }

        return $this->response->setJSON(['status'=>'success','info'=>$info,'backups'=>$backups]);
    }

    // Download a specific backup ZIP from WRITEPATH/backups
    public function downloadBackup()
    {
        $file = $this->request->getGet('file');
        if (!$file) return $this->response->setJSON(['status'=>'error','message'=>'Missing file']);
        $name = basename((string)$file);
        $path = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . $name;
        if (!is_file($path)) return $this->response->setJSON(['status'=>'error','message'=>'File not found']);

        $size = filesize($path);
        return $this->response
            ->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $name . '"')
            ->setHeader('Content-Length', (string)$size)
            ->setBody(file_get_contents($path));
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
        // Default password for newly created admins (from env, fallback to '123456')
        $defaultPassword = getenv('DEFAULT_PASSWORD') ?: '123456';

        // Basic required checks (password set server-side)
        if (!$firstName || !$middleName || !$lastName || !$email || !$username || !$position) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
        }
        // Note: we no longer accept password inputs from the form; the default password
        // below will be hashed and stored. Encourage admins to change their password.

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
            'password'        => password_hash($defaultPassword, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'is_verified'     => 0,
            'profile_picture' => $profilePath,
        ];

        if ($adminModel->insert($data)) {
            // Send email notification to the newly created admin
            try {
                $subject = 'Your Admin Account Has Been Created';
                $loginUrl = site_url('admin/login');
                $html = "<p>Hello " . esc($firstName) . ",</p>" .
                    "<p>An admin account has been created for you.</p>" .
                    "<p><strong>Username:</strong> " . esc($username) . "<br>" .
                    "<strong>Default Password:</strong> 123456</p>" .
                    "<p>Please log in at <a href=\"{$loginUrl}\">{$loginUrl}</a> and change your password immediately.</p>";
                $this->sendEmail($email, trim($firstName . ' ' . $lastName), $subject, $html);
            } catch (\Exception $e) {
                // ignore email failures for now
            }

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

    // Produce a ZIP backup (JSON + CSV + metadata) for all database tables
    public function backup()
    {
        $current = session()->get('superadmin_id');
        if (! $current) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Not authenticated']);
            }
            return redirect()->to(site_url('superadmin/login'));
        }

        // Only allow POST to create backup to avoid accidental GET triggers.
        // However, allow AJAX requests through even if they arrive as GET (some clients
        // may issue an AJAX GET). Prefer POST, but be permissive for AJAX so the UI
        // can trigger backups reliably. Non-AJAX GETs will be redirected.
        if ($this->request->getMethod() !== 'post') {
            if (! $this->request->isAJAX()) {
                return redirect()->to(site_url('superadmin/settings'));
            }
            // continue for AJAX requests even if not POST
        }

        $db = \Config\Database::connect();
        $tables = $db->listTables();

        $tmpDir = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . uniqid('backup_', true);
        if (!is_dir($tmpDir) && !@mkdir($tmpDir, 0775, true)) {
            return $this->response->setJSON(['status'=>'error','message'=>'Failed to create temp directory']);
        }

        $counts = [];
        $batchSize = 500;

        foreach ($tables as $table) {
            // Skip if we cannot get fields for the table
            try {
                $fields = $db->getFieldNames($table);
            } catch (\Exception $e) {
                $fields = [];
            }

            $csvPath = $tmpDir . DIRECTORY_SEPARATOR . $table . '.csv';
            $jsonPath = $tmpDir . DIRECTORY_SEPARATOR . $table . '.json';

            $fhCsv = fopen($csvPath, 'w');
            $fhJson = fopen($jsonPath, 'w');
            if ($fhCsv === false || $fhJson === false) continue;

            // CSV header
            if (!empty($fields)) {
                fputcsv($fhCsv, $fields);
            }

            // JSON array start
            fwrite($fhJson, "[");
            $firstJson = true;

            $offset = 0;
            $tableCount = 0;
            while (true) {
                $builder = $db->table($table);
                $rows = $builder->limit($batchSize, $offset)->get()->getResultArray();
                if (empty($rows)) break;

                foreach ($rows as $r) {
                    // CSV line matching fields order
                    if (!empty($fields)) {
                        $line = [];
                        foreach ($fields as $f) {
                            $val = isset($r[$f]) ? $r[$f] : '';
                            if (is_array($val) || is_object($val)) $val = json_encode($val);
                            $line[] = $val;
                        }
                        fputcsv($fhCsv, $line);
                    }

                    // JSON write
                    if ($firstJson) {
                        fwrite($fhJson, json_encode($r, JSON_UNESCAPED_UNICODE));
                        $firstJson = false;
                    } else {
                        fwrite($fhJson, ",\n" . json_encode($r, JSON_UNESCAPED_UNICODE));
                    }

                    $tableCount++;
                }

                // advance
                $offset += $batchSize;
            }

            // JSON end
            fwrite($fhJson, "]");
            fclose($fhJson);
            fclose($fhCsv);

            $counts[$table] = $tableCount;
        }

        // metadata
        $meta = [
            'created_at' => date('Y-m-d H:i:s'),
            'requestor_id' => $current,
            'requestor_email' => session()->get('superadmin_email') ?: '',
            'counts' => $counts,
        ];
        file_put_contents($tmpDir . DIRECTORY_SEPARATOR . 'metadata.json', json_encode($meta, JSON_PRETTY_PRINT));

        // Additionally, produce a SQL dump (backup.sql) including CREATE TABLE and INSERT statements.
        // This is a best-effort SQL export constructed from the live schema and rows.
        $sqlPath = $tmpDir . DIRECTORY_SEPARATOR . 'backup.sql';
        try {
            $fpSql = fopen($sqlPath, 'w');
            if ($fpSql !== false) {
                fwrite($fpSql, "-- Backup generated on " . date('Y-m-d H:i:s') . "\n\n");
                foreach ($tables as $table) {
                    try {
                        // Get CREATE TABLE statement
                        $create = '';
                        $res = $db->query("SHOW CREATE TABLE `" . str_replace('`','``',$table) . "`");
                        if ($res) {
                            $row = $res->getRowArray();
                            if ($row) {
                                foreach ($row as $c) { $create = $c; break; }
                            }
                        }

                        if ($create !== '') {
                            fwrite($fpSql, "-- Table structure for `{$table}`\n");
                            fwrite($fpSql, "DROP TABLE IF EXISTS `{$table}`;\n");
                            fwrite($fpSql, $create . ";\n\n");
                        }

                        // Emit INSERT statements in batches
                        $batch = 500;
                        $offset = 0;
                        while (true) {
                            $rows = $db->table($table)->limit($batch, $offset)->get()->getResultArray();
                            if (empty($rows)) break;
                            foreach ($rows as $r) {
                                $cols = array_keys($r);
                                $values = [];
                                foreach ($cols as $c) {
                                    $val = $r[$c];
                                    if ($val === null) {
                                        $values[] = 'NULL';
                                    } else {
                                        // Use the DB connection escape to quote/escape values
                                        try {
                                            $values[] = $db->escape($val);
                                        } catch (\Throwable $_) {
                                            // Fallback: basic addslashes and quoted
                                            $values[] = "'" . addslashes(is_scalar($val) ? (string)$val : json_encode($val)) . "'";
                                        }
                                    }
                                }
                                $colList = '`' . implode('`,`', array_map(function($s){ return str_replace('`','``',$s); }, $cols)) . '`';
                                $valList = implode(', ', $values);
                                fwrite($fpSql, "INSERT INTO `{$table}` ({$colList}) VALUES ({$valList});\n");
                            }
                            $offset += $batch;
                        }
                        fwrite($fpSql, "\n");
                    } catch (\Throwable $_) {
                        // ignore table-level failures and continue
                    }
                }
                fclose($fpSql);
                $counts['backup_sql'] = filesize($sqlPath);
            }
        } catch (\Throwable $_) { /* ignore SQL dump errors */ }

        // Rewrite metadata to include any new counts (e.g., backup_sql size)
        try {
            $meta['counts'] = $counts;
            file_put_contents($tmpDir . DIRECTORY_SEPARATOR . 'metadata.json', json_encode($meta, JSON_PRETTY_PRINT));
        } catch (\Throwable $_) { }

        // Create ZIP
        if (!is_dir(WRITEPATH . 'backups')) @mkdir(WRITEPATH . 'backups', 0775, true);
        $zipName = 'backup-all-' . date('Ymd-His') . '.zip';
        $zipPath = WRITEPATH . 'backups' . DIRECTORY_SEPARATOR . $zipName;
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            array_map('unlink', glob($tmpDir . DIRECTORY_SEPARATOR . '*'));
            @rmdir($tmpDir);
            return $this->response->setJSON(['status'=>'error','message'=>'Failed to create ZIP file']);
        }

        foreach (glob($tmpDir . DIRECTORY_SEPARATOR . '*') as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Log the backup action
        try {
            $logModel = new \App\Models\AdminActivityLogModel();
            $details = ['filename' => $zipName, 'counts' => $counts];
            $logModel->insert([
                'actor_type' => 'superadmin',
                'actor_id'   => $current,
                'action'     => 'backup',
                'route'      => '/superadmin/backup',
                'resource'   => 'all_tables',
                'method'     => 'POST',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => substr((string)($this->request->getUserAgent() ?? ''), 0, 255),
                'details'    => json_encode($details),
            ]);
        } catch (\Throwable $_) { }

        // Send notification email to requester (best-effort)
        try {
            $actorEmail = session()->get('superadmin_email') ?: '';
            if (!empty($actorEmail)) {
                $actorName = session()->get('superadmin_first_name') ? trim(session()->get('superadmin_first_name') . ' ' . session()->get('superadmin_last_name')) : $actorEmail;
                $html = view('emails/action_confirmation', [
                    'recipientName' => $actorName,
                    'actionLabel'   => 'Full Database Backup',
                    'targetName'    => 'All Tables',
                    'targetEmail'   => $actorEmail,
                    'time'          => $meta['created_at'],
                    'actionId'      => null,
                    'phase'         => 'approved',
                ]);
                $this->sendEmail($actorEmail, $actorName, 'Database Backup Completed', $html);
            }
        } catch (\Exception $_) { }

        // Stream ZIP to client (or return JSON when requested via AJAX)
        if (!file_exists($zipPath)) {
            array_map('unlink', glob($tmpDir . DIRECTORY_SEPARATOR . '*'));
            @rmdir($tmpDir);
            return $this->response->setJSON(['status'=>'error','message'=>'ZIP file missing']);
        }
        // If this is an AJAX request, return the filename as JSON so the client
        // can fetch the ZIP via `downloadBackup`. Otherwise, stream the binary.
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'filename' => $zipName, 'message' => 'Backup created']);
        }

        $size = filesize($zipPath);
        $this->response->setHeader('Content-Type', 'application/zip')
                       ->setHeader('Content-Disposition', 'attachment; filename="' . $zipName . '"')
                       ->setHeader('Content-Length', (string)$size);

        $body = fopen($zipPath, 'rb');
        // Cleanup temp files (keep the zip in backups folder)
        foreach (glob($tmpDir . DIRECTORY_SEPARATOR . '*') as $file) { @unlink($file); }
        @rmdir($tmpDir);

        return $this->response->setBody(stream_get_contents($body));
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
        $providedCode = trim($this->request->getPost('admin_code') ?? '');
        $current = session()->get('superadmin_id');
        if (!$current) return $this->response->setJSON(['status' => 'error', 'message' => 'Not authenticated']);
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid admin id']);
        }

        // Require confirmation of acting superadmin's code
        if ($providedCode === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Admin code required for confirmation']);
        }
        $superModel = new \App\Models\SuperAdminModel();
        $currentRow = $superModel->find($current);
        if (!$currentRow || empty($currentRow['admin_code'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid admin code']);
        }

        // Stored admin_code may be hashed (bcrypt) or legacy plaintext.
        // Support three verification paths (in order):
        // 1) Direct equality of the raw provided value to the stored value
        //    (covers the case where someone pastes the stored hash directly)
        // 2) password_verify() when stored is a bcrypt hash and the user provided
        //    the plain alphanumeric code (we normalize before verifying)
        // 3) plaintext equality fallback for legacy rows
        $stored = (string) ($currentRow['admin_code'] ?? '');
        $rawProvided = (string) $providedCode;

        // Normalize the raw provided value by trimming control characters but keep
        // a cleaned alphanumeric-only version for password_verify of generated codes.
        $trimmed = trim($rawProvided);
        $trimmed = str_replace(["\r", "\n", "\t"], '', $trimmed);
        $providedAlnum = preg_replace('/[^A-Za-z0-9]/', '', $trimmed);

        $isValid = false;

        // 1) Direct raw equality (if user pasted the stored hash itself)
        $rawEqual = ($rawProvided !== '' && $rawProvided === $stored);
        if ($rawEqual) $isValid = true;

        // 2) Try password_verify for any supported hash format (bcrypt, argon2, etc.)
        $pwVerified = false;
        if (! $isValid && $providedAlnum !== '') {
            try {
                if (password_verify($providedAlnum, $stored)) {
                    $pwVerified = true;
                    $isValid = true;
                }
            } catch (\Throwable $_) {
                // ignore and continue to fallback
            }
        }

        // 3) Fallback: direct plaintext comparison (covers legacy rows still storing plaintext)
        $plainEqual = (! $isValid && $providedAlnum !== '' && $stored === $providedAlnum);
        if ($plainEqual) $isValid = true;

        // If verification failed, write a concise debug log (no secrets)
        if (! $isValid) {
            $debug = [
                'superadmin_id' => $current,
                'rawProvided_len' => strlen($rawProvided),
                'providedAlnum_len' => strlen($providedAlnum),
                'stored_len' => strlen($stored),
                'rawEqual' => $rawEqual ? 1 : 0,
                'pwVerified' => $pwVerified ? 1 : 0,
                'plainEqual' => $plainEqual ? 1 : 0,
            ];
            if (function_exists('log_message')) {
                log_message('error', 'retireUser: admin_code verification failed: ' . json_encode($debug));
            }

            // In non-production environments, return debugging info to the client
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid admin code', 'debug' => $debug]);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid admin code']);
        }

        if (! $isValid) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid admin code']);
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

        // Attempt to send email notification to the retired admin (best-effort)
        try {
            $targetEmail = $row['email'] ?? '';
            if (!empty($targetEmail)) {
                $targetName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['username'] ?? $targetEmail);
                $actorName = '';
                if (!empty($currentRow)) {
                    $actorName = trim(($currentRow['first_name'] ?? '') . ' ' . ($currentRow['last_name'] ?? '')) ?: ($currentRow['email'] ?? 'Super Admin');
                }
                $time = date('Y-m-d H:i:s');
                $html = view('emails/admin_retired', [
                    'targetName' => $targetName,
                    'actorName'  => $actorName,
                    'username'   => $row['username'] ?? '',
                    'targetEmail'=> $targetEmail,
                    'time'       => $time,
                ]);
                $this->sendEmail($targetEmail, $targetName, 'Your Admin Account Has Been Retired', $html);
            }
        } catch (\Exception $e) {
            // swallow email errors; retiring should not fail because of email
        }

        // Send confirmation to acting superadmin (who performed the retire)
        try {
            $actorEmail = $currentRow['email'] ?? '';
            if (!empty($actorEmail)) {
                $actorName = trim(($currentRow['first_name'] ?? '') . ' ' . ($currentRow['last_name'] ?? '')) ?: $actorEmail;
                $targetName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: ($row['username'] ?? $targetEmail);
                $time = date('Y-m-d H:i:s');
                $html = view('emails/action_confirmation', [
                    'recipientName' => $actorName,
                    'actionLabel'   => 'Admin Retired and Archived',
                    'targetName'    => $targetName,
                    'targetEmail'   => $targetEmail,
                    'time'          => $time,
                    'phase'         => 'approved'
                ]);
                $this->sendEmail($actorEmail, $actorName, 'Admin Retired: Confirmation', $html);
            }
        } catch (\Exception $_) { }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Admin retired and archived.']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/superadmin/login');
    }

    /**
     * Send email using Brevo (Sendinblue) Transactional Emails API.
     */
    private function sendEmail(string $to, string $toName, string $subject, string $htmlBody, string $altBody = ''): bool
    {
        try {
            // Use Brevo (Sendinblue) Transactional Emails API instead of SMTP
            $apiKey = getenv('BREVO_API_KEY') ?: getenv('SENDINBLUE_API_KEY');
            if (empty($apiKey)) {
                if (function_exists('log_message')) log_message('error', 'BREVO_API_KEY not configured');
                return false;
            }

            $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
            $client = new \GuzzleHttp\Client();
            $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi($client, $config);

            $fromEmail = getenv('SMTP_FROM') ?: getenv('MAIL_FROM') ?: (getenv('SMTP_USER') ?: 'no-reply@localhost');
            $fromName = getenv('MAIL_FROM_NAME') ?: getenv('SMTP_FROM_NAME') ?: 'Support';

            $email = new \Brevo\Client\Model\SendSmtpEmail([
                'subject' => $subject,
                'sender' => ['name' => $fromName, 'email' => $fromEmail],
                'to' => [[ 'email' => $to, 'name' => $toName ?: '' ]],
                'htmlContent' => $htmlBody,
                'textContent' => $altBody ?: strip_tags($htmlBody),
            ]);

            $apiInstance->sendTransacEmail($email);
            return true;
        } catch (\Throwable $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Brevo email send failed: ' . $e->getMessage());
            }
            return false;
        }
    }

}
