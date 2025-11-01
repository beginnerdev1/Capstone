<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserInformationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id'     => 1,
                'first_name'  => 'Carlos',
                'last_name'   => 'Reyes',
                'gender'      => 'Male',
                'phone'       => '09171234567',
                'email'       => 'user1@example.com',
                'purok'       => '2',
                'barangay'    => 'San Juan',
                'municipality'=> 'Bayawan',
                'province'    => 'Negros Oriental',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'     => 2,
                'first_name'  => 'Maria',
                'last_name'   => 'Santos',
                'gender'      => 'Female',
                'phone'       => '09987654321',
                'email'       => 'user2@example.com',
                'purok'       => '3',
                'barangay'    => 'Poblacion',
                'municipality'=> 'Bayawan',
                'province'    => 'Negros Oriental',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('user_information')->insertBatch($data);
    }
}
