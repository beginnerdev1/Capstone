<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationsModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'title', 'body', 'url', 'is_read', 'created_at'];
    protected $useTimestamps = false; // we use manual created_at
}
