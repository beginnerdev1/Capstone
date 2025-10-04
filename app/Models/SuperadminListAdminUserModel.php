<?php
namespace App\Models;

use CodeIgniter\Model;

class SuperadminListAdminUserModel extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'username', 'email', 'password', 'position',
        'is_verified', 'otp_code', 'otp_expire', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true; // auto handle created_at/updated_at
}
