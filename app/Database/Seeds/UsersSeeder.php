<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'email'        => 'user1@example.com',
                'password'     => password_hash('user123', PASSWORD_DEFAULT),
                'active'       => 0,
                'is_verified'  => 1,
                'status'        => 'pending',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'email'        => 'user2@example.com',
                'password'     => password_hash('user123', PASSWORD_DEFAULT),
                'active'       => 2,
                'is_verified'  => 0,
                'status'        => 'approved',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
