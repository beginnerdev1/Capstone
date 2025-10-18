<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'Firstname',
        'Surname',
        'username',
        'email',
        'password',
        'Purok',
        'is_verified',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    // 🔍 Get all verified users or filter by purok
    public function getUsers($purok = null)
    {
        $builder = $this->where('is_verified', 1);

        if ($purok) {
            $builder->where('Purok', $purok);
        }

        return $builder->orderBy('Surname', 'ASC')->findAll();
    }

    // 🧭 Find user by username
    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    // 🔐 Verify user login
    public function verifyLogin($username, $password)
    {
        $user = $this->where('username', $username)->first();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
?>