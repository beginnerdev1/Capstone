<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminArchivedModel extends Model
{
    protected $table      = 'admin_archived';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'original_admin_id',
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'email',
        'password',
        'position',
        'is_verified',
        'profile_picture',
        'created_at',
        'updated_at',
        'archived_at'
    ];

    public $useTimestamps = false; // archived_at handled manually
}
