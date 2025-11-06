<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'active',
        'email',
        'password',
        'is_verified',
        'otp_code',
        'otp_expires',
        'created_at',
        'updated_at',
        'status',
        
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';

    /**
     * ✅ Get all verified users (with optional info join)
     */
    public function getVerifiedUsers($withInfo = false)
    {
        $builder = $this->where('is_verified', 1);

        if ($withInfo) {
            $builder = $builder
                ->select('users.*, user_information.first_name, user_information.last_name, user_information.purok, user_information.barangay')
                ->join('user_information', 'user_information.user_id = users.id', 'left');
        }

        return $builder->orderBy('email', 'ASC')->findAll();
    }

    /**
     * 🧭 Find user by email
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * 🔐 Verify user login credentials
     */
    public function verifyLogin($email, $password)
    {
        $user = $this->where('email', $email)->first();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    /**
     * 👤 Get user with information (joined)
     */
    public function getUserWithInfo($userId)
    {
        return $this->select('users.*, user_information.*')
                    ->join('user_information', 'user_information.user_id = users.id', 'left')
                    ->where('users.id', $userId)
                    ->first();
    }
}
