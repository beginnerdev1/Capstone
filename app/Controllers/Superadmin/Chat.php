<?php

namespace App\Controllers\Superadmin;

use App\Controllers\Admin\Chat as AdminChat;
use App\Models\AdminChatModel;
use App\Models\AdminModel;

/**
 * Superadmin chat wrapper — reuses Admin\Chat behavior while running
 * under the superadmin namespace so sidebar links and routes are
 * consistent for superadmins.
 */
class Chat extends AdminChat
{
    // Override index to load the superadmin-specific chat view
    public function index()
    {
        return view('superadmin/chat');
    }
    // Return list of admins (id, name, email, phone if available) so superadmins can chat to admins
    public function getAdmins()
    {
        $model = new AdminModel();
        $rows = $model->select('id, first_name, last_name, email')
            ->orderBy('first_name')->findAll();

        $hasPhone = false;
        try {
            $hasPhone = (bool) ($model->db->fieldExists('phone', $model->table));
        } catch (\Throwable $_) {
            $hasPhone = false;
        }

        $list = [];
        foreach ($rows as $a) {
            $list[] = [
                'id' => $a['id'],
                'name' => trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '')) ?: ($a['email'] ?? 'Admin'),
                'email' => $a['email'] ?? '',
                'phone' => $hasPhone && isset($a['phone']) ? $a['phone'] : '',
            ];
        }

        return $this->response->setJSON($list);
    }
    // Override getMessages to return only admin/internal messages (no user messages)
    public function getMessages()
    {
        $since = $this->request->getGet('since');
        $builder = $this->chatModel->whereIn('sender', ['admin', 'admin_internal'])->orderBy('created_at', 'ASC');
        if ($since) {
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }
        $messages = $builder->findAll() ?: [];
        return $this->response->setJSON($messages);
    }

    // Override postMessage so superadmins post internal admin-only messages
    public function postMessage()
    {
        $session = session();
        // Prefer superadmin id when present so superadmins send as themselves
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;

        if (! $adminId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);
        }

        $text = $this->request->getPost('message');
        if (! $text) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Message required']);
        }

        $db = \Config\Database::connect();
        $table = $this->chatModel->table;

        // Accept client-supplied external_id to make posts idempotent
        $external = $this->request->getPost('external_id') ?: bin2hex(random_bytes(6));
        try {
            $existing = $this->chatModel->where('external_id', $external)->first();
            if ($existing) {
                return $this->response->setJSON($existing);
            }
        } catch (\Throwable $_) {
            // continue
        }

        $data = [
            'external_id' => $external,
            'admin_id' => $adminId,
            'user_id' => null, // internal channel
            'sender' => 'admin_internal',
            'message' => $text,
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            if ($db->fieldExists('admin_recipient_id', $table)) {
                $data['admin_recipient_id'] = null;
            }
            if ($db->fieldExists('is_internal', $table)) {
                $data['is_internal'] = 1;
            }
        } catch (\Exception $e) {
            // ignore
        }

        try {
            $insertId = $this->chatModel->insert($data);
            $data['id'] = $insertId;
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'superadmin postMessage failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to save message']);
        }
    }

    // Return messages for admin <-> admin conversation between current admin and $recipientId
    public function getMessagesForAdmin($recipientId = null)
    {
        $session = session();
        // Prefer superadmin id when present so superadmins fetch as themselves
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;
        if (! $adminId) return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);

        if (! $recipientId) return $this->response->setJSON([]);

        $since = $this->request->getGet('since');

        // Ensure the DB schema has `admin_recipient_id` to support admin<->admin conversations
        $db = \Config\Database::connect();
        // If feature flag enabled, read from `admin_chats` table
        $chatConfig = config('Chat');
        if (! empty($chatConfig->useAdminChats) && isset($this->adminChatModel)) {
            $rows = $this->adminChatModel->getConversationBetween($adminId, $recipientId, $since) ?: [];
            // Map admin_chats shape to legacy chat_messages shape expected by the views
            $mapped = array_map(function($r){
                return [
                    'id' => $r['id'] ?? null,
                    'external_id' => $r['external_id'] ?? null,
                    'admin_id' => $r['sender_admin_id'] ?? null,
                    'admin_recipient_id' => $r['recipient_admin_id'] ?? null,
                    'sender' => 'admin_internal',
                    'is_internal' => !empty($r['is_broadcast']) ? 1 : 0,
                    'message' => $r['message'] ?? '',
                    'created_at' => $r['created_at'] ?? null,
                    'read_at' => $r['read_at'] ?? null,
                ];
            }, $rows);

            return $this->response->setJSON($mapped);
        }

        // Fallback to legacy chat_messages based implementation
        try {
            if (! $db->fieldExists('admin_recipient_id', $this->chatModel->table)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'admin_recipient_id column missing; run migrations']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Unable to verify schema', 'exception' => $e->getMessage()]);
        }

        // messages where (admin_id = adminId AND admin_recipient_id = recipientId)
        // or (admin_id = recipientId AND admin_recipient_id = adminId)
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

    // Post an admin->admin internal message to a specific admin
    public function postAdminMessage()
    {
        $session = session();
        // Prefer superadmin id when present so superadmins post as themselves
        $adminId = $session->get('superadmin_id') ?? $session->get('admin_id') ?? null;

        if (! $adminId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);
        }

        $text = $this->request->getPost('message');
        $recipient = $this->request->getPost('recipient_admin_id');
        if (! $text || ! $recipient) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Message and recipient required']);
        }

        $db = \Config\Database::connect();
        $table = $this->chatModel->table;

        $external = $this->request->getPost('external_id') ?: bin2hex(random_bytes(6));
        try {
            $existing = $this->chatModel->where('external_id', $external)->first();
            if ($existing) {
                return $this->response->setJSON($existing);
            }
        } catch (\Throwable $_) {
            // continue
        }

        $data = [
            'external_id' => $external,
            'admin_id' => $adminId,
            'user_id' => null,
            'sender' => 'admin_internal',
            'message' => $text,
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            if ($db->fieldExists('admin_recipient_id', $table)) {
                $data['admin_recipient_id'] = (int)$recipient;
            }
            if ($db->fieldExists('is_internal', $table)) {
                $data['is_internal'] = 1;
            }
        } catch (\Exception $e) {
            // ignore
        }

        try {
            $insertId = $this->chatModel->insert($data);
            $data['id'] = $insertId;
            // Also write to admin_chats table for cutover
            try {
                // ensure adminChatModel exists on parent
                if (! isset($this->adminChatModel)) {
                    $this->adminChatModel = new AdminChatModel();
                }
                $adminData = [
                    'external_id' => $data['external_id'] ?? null,
                    'sender_admin_id' => (int)$adminId,
                    'recipient_admin_id' => (int)$recipient,
                    'message' => $text,
                    'is_read' => 1,
                    'is_broadcast' => 0,
                    'created_at' => $data['created_at'],
                ];
                $this->adminChatModel->insert($adminData);
            } catch (\Throwable $e) {
                log_message('warning', 'superadmin adminChatModel insert failed: ' . $e->getMessage());
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'superadmin postAdminMessage failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to save message']);
        }
    }
}
