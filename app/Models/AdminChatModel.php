<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminChatModel extends Model
{
    protected $table = 'admin_chats';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'external_id',
        'sender_admin_id',
        'recipient_admin_id',
        'message',
        'is_read',
        'read_at',
        'is_broadcast',
        'metadata',
        'created_at',
    ];

    // Simple helper: return messages between two admins (both directions)
    public function getConversationBetween($a, $b, $since = null)
    {
        $builder = $this->builder();
        $builder->groupStart()
                ->where('sender_admin_id', (int)$a)->where('recipient_admin_id', (int)$b)
            ->groupEnd()
            ->orGroupStart()
                ->where('sender_admin_id', (int)$b)->where('recipient_admin_id', (int)$a)
            ->groupEnd()
            ->orderBy('created_at', 'ASC');

        if ($since) {
            $ts = date('Y-m-d H:i:s', strtotime($since));
            $builder->where('created_at >', $ts);
        }

        return $builder->get()->getResultArray();
    }

    // Return list of recent partners for admin
    public function getConversationsForAdmin($adminId)
    {
        $adminId = (int)$adminId;
        $db = $this->db;
        $sql = "SELECT * FROM admin_chats WHERE sender_admin_id = ? OR recipient_admin_id = ? ORDER BY created_at DESC";
        $rows = $db->query($sql, [$adminId, $adminId])->getResultArray();
        return $rows;
    }

    // after inserting into legacy chat_messages
    public function insertChatMessage($data)
    {
        $insertId = $this->chatModel->insert($data);
        log_message('info', "chat_messages inserted id={$insertId} external={$data['external_id']}");

        // after inserting into admin_chats (duplicate)
        try {
            $this->adminChatModel->insert($adminData);
            log_message('info', "admin_chats inserted ext={$adminData['external_id']} sender={$adminData['sender_admin_id']} recipient={$adminData['recipient_admin_id']}");
        } catch (\Throwable $e) {
            log_message('warning', 'admin_chats insert failed: ' . $e->getMessage());
        }
    }
}
