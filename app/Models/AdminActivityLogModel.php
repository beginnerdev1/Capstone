<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminActivityLogModel extends Model
{
    protected $table      = 'admin_activity_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'actor_type', 'actor_id', 'action', 'route', 'method', 'resource', 'details', 'ip_address', 'user_agent', 'created_at', 'logged_out_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
