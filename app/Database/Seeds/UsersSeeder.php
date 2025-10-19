<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'is_verified' => 1,
            ],
            [
                'email' => 'johndoe@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'is_verified' => 1,
            ],
            [
                'email' => 'janesmith@example.com',
                'password' => password_hash('mypassword', PASSWORD_DEFAULT),
                'is_verified' => 0,
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
