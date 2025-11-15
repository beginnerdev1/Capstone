<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsersModel;
use App\Models\UserInformationModel;

class ApprovedUsersPerPurokSeeder extends Seeder
{
    public function run()
    {
        $usersModel = new UsersModel();
        $infoModel = new UserInformationModel();

        // There are 6 puroks. 3 users per purok = 18 total.
        for ($purok = 1; $purok <= 6; $purok++) {
            for ($i = 1; $i <= 3; $i++) {

                $email = "purok{$purok}_user{$i}@mail.com";

                // Avoid duplicates if run again
                $existing = $usersModel->where('email', $email)->first();
                if ($existing) {
                    continue;
                }

                // Insert user
                $userId = $usersModel->insert([
                    'email' => $email,
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'active' => 1,
                    'status' => 'approved',
                    'is_verified' => 1,
                    'profile_complete' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], true);

                // Insert user info
                $infoModel->insert([
                    'user_id' => $userId,
                    'first_name' => "User{$i}",
                    'last_name' => "Purok{$purok}",
                    'gender' => 'Male',
                    'age' => 25,
                    'family_number' => rand(1, 4),
                    'phone' => '09123456789',
                    'purok' => $purok,
                    'barangay' => 'Borlongan',
                    'municipality' => 'Dipaculao',
                    'province' => 'Aurora',
                    'zipcode' => '3203',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}

