<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Super Admin',
                'username'    => 'admin',
                'email'       => 'admin@example.com',
                'password'    => password_hash('admin123', PASSWORD_DEFAULT),
                'position'    => 'Administrator',
                'is_verified' => 1,
                'otp_code'    => null,
                'otp_expire'  => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data into 'admin' table
        $this->db->table('admin')->insertBatch($data);
    }
}
