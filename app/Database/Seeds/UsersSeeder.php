<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'Surname'      => 'Cruz',
                'Firstname'    => 'Juan',
                'Middlename'   => 'D',
                'username'     => 'juancruz',
                'Purok'        => 1,
                'Barangay'     => 'San Isidro',
                'Municipality' => 'Baliwag',
                'Province'     => 'Bulacan',
                'email'        => 'juan@example.com',
                'password'     => password_hash('Password@123', PASSWORD_DEFAULT),
                'is_verified'  => 1,
                'created_at'   => Time::now(),
                'updated_at'   => Time::now(),
            ],
            [
                'Surname'      => 'Reyes',
                'Firstname'    => 'Maria',
                'Middlename'   => 'L',
                'username'     => 'mariareyes',
                'Purok'        => 2,
                'Barangay'     => 'San Jose',
                'Municipality' => 'Baliwag',
                'Province'     => 'Bulacan',
                'email'        => 'maria@example.com',
                'password'     => password_hash('Password@123', PASSWORD_DEFAULT),
                'is_verified'  => 1,
                'created_at'   => Time::now(),
                'updated_at'   => Time::now(),
            ],
            [
                'Surname'      => 'Santos',
                'Firstname'    => 'Pedro',
                'Middlename'   => 'A',
                'username'     => 'pedrosantos',
                'Purok'        => 3,
                'Barangay'     => 'Sta. Maria',
                'Municipality' => 'Baliwag',
                'Province'     => 'Bulacan',
                'email'        => 'pedro@example.com',
                'password'     => password_hash('Password@123', PASSWORD_DEFAULT),
                'is_verified'  => 0,
                'created_at'   => Time::now(),
                'updated_at'   => Time::now(),
            ],
        ];

        // Insert all data
        $this->db->table('users')->insertBatch($data);
    }
}