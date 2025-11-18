<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'external_id', 'user_id', 'admin_id', 'sender', 'user_name', 'message', 'is_read', 'read_at', 'created_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}
