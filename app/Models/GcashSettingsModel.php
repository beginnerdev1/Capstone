<?php

namespace App\Models;

use CodeIgniter\Model;

class GcashSettingModel extends Model
{
    protected $table          = 'gcash_settings';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false; // No soft deletes needed here

    protected $allowedFields = [
        'gcash_number', 
        'qr_code_path',
    ];

    // Enable CI4's automatic timestamp tracking
    protected $useTimestamps = true; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}