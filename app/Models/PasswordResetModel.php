<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table      = 'password_resets';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'email',
        'token',
        'expires_at',
        'created_at',
    ];

    // (optional) auto return as array
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
