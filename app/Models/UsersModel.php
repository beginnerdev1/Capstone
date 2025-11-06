<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'email',
        'password',
        'active',
        'status',
        'is_verified',
        'profile_complete',
        'otp_code',
        'otp_expires',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';

    // Get all approved users (optionally join with user_information)
    public function getApprovedUsers($withInfo = false)
    {
        $builder = $this->where('status', 'approved');

        if ($withInfo) {
            $builder = $builder
                ->select('users.*, user_information.first_name, user_information.last_name, user_information.purok, user_information.barangay')
                ->join('user_information', 'user_information.user_id = users.id', 'left');
        }

        return $builder->orderBy('email', 'ASC')->findAll();
    }

    // Find user by email
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    // Verify login (only for approved, active, and verified users)
    public function verifyLogin($email, $password)
    {
        $user = $this->where('email', $email)->first();

        if (
            $user &&
            $user['active'] == 1 &&
            $user['status'] == 'approved' &&
            $user['is_verified'] == 1 &&
            password_verify($password, $user['password'])
        ) {
            return $user;
        }

        return null;
    }

    // Get user with joined info
    public function getUserWithInfo($userId)
    {
        return $this->select('users.*, user_information.*')
                    ->join('user_information', 'user_information.user_id = users.id', 'left')
                    ->where('users.id', $userId)
                    ->first();
    }
}
