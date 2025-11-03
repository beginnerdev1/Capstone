<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table            = 'admin';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'username',
        'email',
        'password',
        'position',
        'is_verified',
        'otp_code',
        'otp_expire',
        'created_at',
        'updated_at',
        'first_name',
        'last_name',
        'middle_name',
        'profile_picture',
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}
