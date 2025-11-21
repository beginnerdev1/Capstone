<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\ChatMessageModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    protected $adminModel;
    protected $chatModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->chatModel = new ChatMessageModel();
    }

    // Admin-only chat room UI
    public function index()
    {
        return view('admin/chat');
    }

    // Return list of admins (id, name, email, phone if available)
    public function getAdmins()
    {
        $admins = $this->adminModel->select('id, first_name, last_name, email')
            ->orderBy('first_name')->findAll();

        // Determine if `phone` column exists on `admin` table. Don't rely on getAllowedFields()
        // (may not be available on older CI4 versions); use the model's DB connection.
        $hasPhone = false;
        try {
            $hasPhone = (bool) ($this->adminModel->db->fieldExists('phone', $this->adminModel->table));
        } catch (\Throwable $_) {
            $hasPhone = false;
        }

        $list = [];
        foreach ($admins as $a) {
            $list[] = [
                'id' => $a['id'],
                'name' => trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '')) ?: ($a['email'] ?? 'Unknown'),
                'email' => $a['email'] ?? '',
                'phone' => $hasPhone && isset($a['phone']) ? $a['phone'] : '',
            ];
        }

        return $this->response->setJSON($list);
    }

    // Return all chat messages (admin view)
    public function getMessages()
    {
        $since = $this->request->getGet('since');
        $builder = $this->chatModel->orderBy('created_at', 'ASC');
        if ($since) {
            // accept ISO or timestamp
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }
        $messages = $builder->findAll();
        return $this->response->setJSON($messages ?: []);
    }

    // Post a message as admin; optional user_id to target a user
    public function postMessage()
    {
        $session = session();
        // Allow superadmin to act as admin in chat UI — prefer superadmin when present
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;

        $text = $this->request->getPost('message');
        if (! $text) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => 'Message required']);
        }

        $admins = $this->adminModel->find($adminId);
        $author = $admins ? trim(($admins['first_name'] ?? '') . ' ' . ($admins['last_name'] ?? '')) : 'Admin';

        $targetUserId = $this->request->getPost('user_id');

        $data = [
            'external_id' => bin2hex(random_bytes(6)),
            'admin_id' => $adminId,
            'user_id' => $targetUserId ? (int)$targetUserId : null,
            'sender' => 'admin',
            'message' => $text,
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $insertId = $this->chatModel->insert($data);
        $data['id'] = $insertId;

        return $this->response->setJSON($data);
    }

    // Return number of unread user messages for admins (sender = 'user' and is_read = 0)
    public function unreadCount()
    {
        $count = $this->chatModel->where('sender', 'user')->where('is_read', 0)->countAllResults();
        return $this->response->setJSON(['count' => (int)$count]);
    }

    // Import existing JSON messages into DB. Safe to run multiple times (skips duplicates based on external_id).
    public function importJson()
    {
        $file = WRITEPATH . 'chat' . DIRECTORY_SEPARATOR . 'messages.json';
        if (! file_exists($file)) {
            return $this->response->setJSON(['imported' => 0, 'message' => 'No JSON export found']);
        }

        $json = file_get_contents($file);
        $items = json_decode($json, true) ?: [];
        $imported = 0;

        foreach ($items as $it) {
            $external = isset($it['id']) ? $it['id'] : md5(($it['author'] ?? '') . '|' . ($it['message'] ?? '') . '|' . ($it['created_at'] ?? ''));

            // Skip if already imported
            $exists = $this->chatModel->where('external_id', $external)->countAllResults();
            if ($exists) continue;

            // Determine sender
            $sender = 'user';
            if (isset($it['author_id']) && $it['author_id']) {
                // treat as admin if author_id matches an admin
                $maybeAdmin = $this->adminModel->find($it['author_id']);
                if ($maybeAdmin) {
                    $sender = 'admin';
                }
            }

            $record = [
                'external_id' => $external,
                'user_id' => isset($it['user_id']) ? (int)$it['user_id'] : (isset($it['author_id']) && $sender === 'user' ? (int)$it['author_id'] : null),
                'admin_id' => ($sender === 'admin' && isset($it['author_id'])) ? (int)$it['author_id'] : null,
                'sender' => $sender,
                'message' => $it['message'] ?? '',
                'is_read' => ($sender === 'user') ? 0 : 1,
                'created_at' => isset($it['created_at']) ? date('Y-m-d H:i:s', strtotime($it['created_at'])) : date('Y-m-d H:i:s'),
            ];

            $this->chatModel->insert($record);
            $imported++;
        }

        return $this->response->setJSON(['imported' => $imported]);
    }

    // Return conversation list (distinct users who have messages)
    public function getConversations()
    {
        $usersModel = new UsersModel();

        // Get distinct user_ids that exist in chat_messages
        $rows = $this->chatModel->select('user_id')->where('user_id IS NOT NULL')->groupBy('user_id')->findAll();
        $userIds = array_map(function($r){ return (int)$r['user_id']; }, $rows ?: []);

        $convs = [];
        foreach ($userIds as $uid) {
            $last = $this->chatModel->where('user_id', $uid)->orderBy('created_at', 'DESC')->first();
            $unread = $this->chatModel->where('user_id', $uid)->where('sender', 'user')->where('is_read', 0)->countAllResults();
             // getUserWithInfo joins user_information to include first_name/last_name
            $user = $usersModel->getUserWithInfo($uid);

            // Prefer explicit user first/last name from Users table when available.
            // Otherwise, fall back to `user_name` stored on the latest message (if present),
            // and finally to a generic fallback with the user id.
            $display = null;
            if ($user) {
                $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                if ($name !== '') {
                    $display = $name;
                }
            }

            if (empty($display) && ! empty($last['user_name'])) {
                $display = $last['user_name'];
            }

            if (empty($display)) {
                // Do NOT expose raw user IDs in the UI for privacy/security.
                $display = 'User';
            }

            $convs[] = [
                'user_id' => $uid,
                'display' => $display,
                'last_message' => $last['message'] ?? '',
                'last_at' => $last['created_at'] ?? null,
                'unread' => (int)$unread,
            ];
        }

        // sort by last_at desc
        usort($convs, function($a,$b){
            $ta = $a['last_at'] ? strtotime($a['last_at']) : 0;
            $tb = $b['last_at'] ? strtotime($b['last_at']) : 0;
            return $tb <=> $ta;
        });

        return $this->response->setJSON($convs);
    }

    // Return messages for an admin <-> admin conversation
    public function getMessagesForAdmin($recipientId = null)
    {
        $session = session();
        // Prefer superadmin id when available so superadmins post as themselves
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;
        if (! $adminId) return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);

        if (! $recipientId) return $this->response->setJSON([]);

        $since = $this->request->getGet('since');

        // Ensure the DB schema has `admin_recipient_id` to support admin<->admin conversations
        $db = \Config\Database::connect();
        if (! $db->fieldExists('admin_recipient_id', $this->chatModel->table)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'admin_recipient_id column missing; run migrations']);
        }

        $builder = $this->chatModel->groupStart()
            ->where('admin_id', (int)$adminId)->where('admin_recipient_id', (int)$recipientId)
            ->groupEnd()
            ->orGroupStart()
            ->where('admin_id', (int)$recipientId)->where('admin_recipient_id', (int)$adminId)
            ->groupEnd()
            ->orderBy('created_at', 'ASC');

        if ($since) {
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }

        $messages = $builder->findAll() ?: [];
        return $this->response->setJSON($messages);
    }

    // Non-destructive audit endpoint: return legacy broadcasts where admin_recipient_id IS NULL
    public function getBroadcasts()
    {
        $session = session();
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;
        if (! $adminId) return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);

        $since = $this->request->getGet('since');

        $db = \Config\Database::connect();
        // Ensure the `admin_recipient_id` column exists before querying for NULLs
        try {
            if (! $db->fieldExists('admin_recipient_id', $this->chatModel->table)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'admin_recipient_id column missing; run migrations']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to verify schema', 'exception' => $e->getMessage()]);
        }

        $builder = $this->chatModel->where('sender', 'admin_internal')->where('admin_recipient_id', null)->orderBy('created_at', 'DESC');
        if ($since) {
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }

        $messages = $builder->findAll() ?: [];
        return $this->response->setJSON($messages);
    }

    // Post an admin->admin internal message
    public function postAdminMessage()
    {
        $session = session();
        // Prefer superadmin id when available so superadmins fetch as themselves
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;

        if (! $adminId) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['error' => 'Admin required']);
        }

        $text = $this->request->getPost('message');
        $recipient = $this->request->getPost('recipient_admin_id');
        if (! $text || ! $recipient) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => 'Message and recipient required']);
        }

        $db = \Config\Database::connect();
        $table = $this->chatModel->table;

        $data = [
            'external_id' => bin2hex(random_bytes(6)),
            'admin_id' => $adminId,
            'user_id' => null,
            'sender' => 'admin_internal',
            'message' => $text,
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Only include admin_recipient_id and is_internal if the DB schema has them
        try {
            if ($db->fieldExists('admin_recipient_id', $table)) {
                $data['admin_recipient_id'] = (int)$recipient;
            }
            if ($db->fieldExists('is_internal', $table)) {
                $data['is_internal'] = 1;
            }
        } catch (\Exception $e) {
            // ignore field check failures; proceed without optional fields
        }

        try {
            $insertId = $this->chatModel->insert($data);
            $data['id'] = $insertId;
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'postAdminMessage insert failed: ' . $e->getMessage());
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['error' => 'Failed to save message']);
        }
    }

    // Return messages for a specific user conversation
    public function getMessagesFor($userId = null)
    {
        if (! $userId) return $this->response->setJSON([]);
        $since = $this->request->getGet('since');
        $builder = $this->chatModel->where('user_id', (int)$userId)->orderBy('created_at', 'ASC');
        if ($since) {
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }
        $messages = $builder->findAll() ?: [];

        // Include a friendly author field for admin UI: use the user's first_name when available
        $user = (new UsersModel())->getUserWithInfo((int)$userId);
        $firstName = $user['first_name'] ?? null;

        $out = [];
        foreach ($messages as $m) {
            $author = 'System';
            if (isset($m['sender']) && $m['sender'] === 'admin') {
                $author = 'Admin';
            } elseif (isset($m['sender']) && $m['sender'] === 'user') {
                if (!empty($firstName)) {
                    $author = $firstName;
                } elseif (!empty($m['user_name'])) {
                    // fallback to stored user_name if first_name not available
                    // but only take the first token (to approximate first name)
                    $parts = preg_split('/\s+/', trim($m['user_name']));
                    $author = $parts[0] ?? 'User';
                } else {
                    $author = 'User';
                }
            }

            $out[] = array_merge($m, ['author' => $author]);
        }

        return $this->response->setJSON($out);
    }

    // Mark user messages as read for a given user
    public function markRead($userId = null)
    {
        if (! $userId) return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => 'user required']);

        $now = date('Y-m-d H:i:s');
        $table = $this->chatModel->table;
        $db = \Config\Database::connect();
        $db->table($table)
            ->where('user_id', (int)$userId)
            ->where('sender', 'user')
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => $now]);

        return $this->response->setJSON(['marked' => true]);
    }

    // Debug helper: return whether `user_name` column exists on `chat_messages`.
    public function hasUserNameColumn()
    {
        $exists = false;
        try {
            $exists = (bool) $this->db->fieldExists('user_name', $this->chatModel->table);
        } catch (\Exception $e) {
            // ignore; will return false
        }
        return $this->response->setJSON(['user_name_column' => $exists]);
    }

    // Admin utility: normalize participant columns so only the proper id is set for each sender.
    // - For messages with sender='user' clear any admin_id
    // - For messages with sender='admin' clear any admin_id that shouldn't be present? (kept for audit)
    // Returns counts of rows updated. Admin-only route.
    public function fixParticipantFields()
    {
        $session = session();
        // Prefer superadmin when available for utilities invoked by superadmins
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;
        if (! $adminId) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['error' => 'Admin required']);
        }

        $table = $this->chatModel->table;
        $db = \Config\Database::connect();
        $affected = ['cleared_admin_on_user_sender' => 0, 'cleared_user_on_admin_sender' => 0];

        // Clear admin_id on messages that were sent by users
        try {
            $res = $db->table($table)
                ->where('sender', 'user')
                ->where('admin_id IS NOT NULL')
                ->update(['admin_id' => null]);
            // rowCount may not be available; fetch count separately
            $affected['cleared_admin_on_user_sender'] = $db->table($table)->where('sender', 'user')->where('admin_id IS NOT NULL')->countAllResults();
        } catch (\Exception $e) {
            // ignore and report 0
            log_message('error', 'fixParticipantFields clear admin_id failed: ' . $e->getMessage());
        }

        // Optionally: clear user_id on messages that were sent by admins but shouldn't have a user target
        // We'll clear user_id only on admin messages where user_id is NULL or invalid; here we leave admin->user messages intact.

        return $this->response->setJSON(['fixed' => $affected]);
    }

    // Admin utility: backfill `user_name` on chat messages from the `users` table where possible.
    public function backfillUserNames()
    {
        $session = session();
        // Prefer superadmin when available for utilities invoked by superadmins
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;
        if (! $adminId) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)->setJSON(['error' => 'Admin required']);
        }

        $usersModel = new UsersModel();
        $table = $this->chatModel->table;
        $db = \Config\Database::connect();

        $updated = 0;
        // Ensure the `user_name` column exists before attempting backfill
        try {
            if (! $db->fieldExists('user_name', $table)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)->setJSON(['error' => 'user_name column missing; run migrations first']);
            }
        } catch (\Exception $e) {
            log_message('error', 'backfillUserNames fieldExists check failed: ' . $e->getMessage());
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['error' => 'Unable to verify schema', 'exception' => $e->getMessage()]);
        }

        try {
            // Find rows needing a user_name where user_id is set
            $rows = $db->table($table)->select('id, user_id')->where('user_id IS NOT NULL')->where('user_name IS NULL')->get()->getResultArray();
            foreach ($rows as $r) {
                $uid = (int) $r['user_id'];
                // Try to get user with joined info (user_information)
                $user = $usersModel->getUserWithInfo($uid);
                $name = null;
                if ($user) {
                    $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['email'] ?? null);
                }
                if ($name) {
                    $db->table($table)->where('id', (int)$r['id'])->update(['user_name' => $name]);
                    $updated++;
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'backfillUserNames failed: ' . $e->getMessage());
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['error' => 'Backfill failed', 'exception' => $e->getMessage()]);
        }

        return $this->response->setJSON(['updated' => $updated]);
    }
}
