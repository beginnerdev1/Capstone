<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table      = 'admin';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'email',
        'username',
        'position',
        'is_verified',
        'created_at',
        'updated_at',
    ];
}