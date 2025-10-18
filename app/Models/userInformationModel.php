<?php

namespace App\Models;

use CodeIgniter\Model;

class UserInformationModel extends Model
{
    protected $table      = 'user_information'; // matches migration
    protected $primaryKey = 'user_id';          // primary key

    protected $allowedFields = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'phone',
        'email',
        'purok',
        'barangay',
        'municipality',
        'province',
        'profile_picture',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;  // automatically handle created_at and updated_at
    protected $returnType     = 'array'; // optional: return results as arrays
}
