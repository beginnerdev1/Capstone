<?php

namespace App\Models;

use CodeIgniter\Model;

class UserInformationModel extends Model
{
    protected $table      = 'user_information';
    protected $primaryKey = 'user_id';

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

    protected $useTimestamps = true;
    protected $returnType = 'array';

    /**
     * ✅ Get full name for a specific user
     */
    public function getFullName($userId)
    {
        $info = $this->where('user_id', $userId)->first();
        return $info ? "{$info['first_name']} {$info['last_name']}" : 'Unknown User';
    }

    /**
     * 📋 Get all user profiles (for admin)
     */
    public function getAllProfiles()
    {
        return $this->select('user_information.*, users.email')
                    ->join('users', 'users.id = user_information.user_id', 'left')
                    ->orderBy('user_information.last_name', 'ASC')
                    ->findAll();
    }
}
