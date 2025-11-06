<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserInformationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'info_id'    => 1,
                'user_id'     => 1,
                'first_name'  => 'Carlos',
                'last_name'   => 'Reyes',
                'gender'      => 'Male',
                'age'         => 30,
                'family_number' => 4,
                'phone'       => '09171234567',
                'purok'       => '2',
                'barangay'    => 'San Juan',
                'municipality'=> 'Bayawan',
                'province'    => 'Negros Oriental',
                'zipcode'     => '3200',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'info_id'     => 2,
                'user_id'     => 2,
                'first_name'  => 'Maria',
                'last_name'   => 'Santos',
                'gender'      => 'Female',
                'age'         => 21,
                'family_number' => 1,
                'phone'       => '09987654321',
                'purok'       => '3',
                'barangay'    => 'Poblacion',
                'municipality'=> 'Bayawan',
                'province'    => 'Negros Oriental',
                'zipcode'     => '3200',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('user_information')->insertBatch($data);
    }
}
