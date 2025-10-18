<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    // Only include the fields that exist in the table
    protected $allowedFields = [
        'email',
        'password',
        'is_verified',
        'otp_code',
        'otp_expires',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    // 🔍 Get all verified users
    public function getUsers()
    {
        return $this->where('is_verified', 1)
                    ->orderBy('email', 'ASC')
                    ->findAll();
    }

    // 🧭 Find user by email
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    // 🔐 Verify user login
    public function verifyLogin($email, $password)
    {
        $user = $this->where('email', $email)->first();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
