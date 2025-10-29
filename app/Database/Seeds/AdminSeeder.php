<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'first_name'  => 'John',
                'middle_name' => 'A.',
                'last_name'   => 'Doe',
                'username'    => 'admin',
                'email'       => 'admin@example.com',
                'password'    => password_hash('admin123', PASSWORD_DEFAULT),
                'position'    => 'Administrator',
                'is_verified' => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'first_name'  => 'Jane',
                'middle_name' => 'B.',
                'last_name'   => 'Smith',
                'username'    => 'manager',
                'email'       => 'manager@example.com',
                'password'    => password_hash('manager123', PASSWORD_DEFAULT),
                'position'    => 'Billing Officer',
                'is_verified' => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('admin')->insertBatch($data);
    }
}
