<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserInformationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id'    => 1,
                'first_name' => 'Admin',
                'last_name'  => 'User',
                'email'      => 'admin@myapp.com',
                'phone'      => '09171234567',
                'barangay'   => 'Poblacion',
                'purok'      => '1'
            ],
            [
                'user_id'    => 2,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'john@example.com',
                'phone'      => '09181234567',
                'barangay'   => 'San Roque',
                'purok'      => '2'
            ],
            [
                'user_id'    => 3,
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'email'      => 'jane@example.com',
                'phone'      => '09191234567',
                'barangay'   => 'San Isidro',
                'purok'      => '3'
            ],
        ];

        $this->db->table('user_information')->insertBatch($data);
    }
}
