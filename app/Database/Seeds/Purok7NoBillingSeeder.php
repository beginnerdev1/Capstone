<?php



namespace App\Database\Seeds;



use CodeIgniter\Database\Seeder;

use App\Models\UsersModel;

use App\Models\UserInformationModel;



class Purok7NoBillingSeeder extends Seeder

{

    public function run()

    {

        $usersModel = new UsersModel();

        $infoModel = new UserInformationModel();



        $email = "caspequiel27@gmail.com";



        $existing = $usersModel->where('email', $email)->first();

        if ($existing) {

            return;

        }



        $userId = $usersModel->insert([

            'email' => $email,

            'password' => password_hash('password123', PASSWORD_DEFAULT),

            'active' => 2,

            'status' => 'approved',

            'is_verified' => 1,

            'profile_complete' => 1,

            'created_at' => date('Y-m-d H:i:s'),

            'updated_at' => date('Y-m-d H:i:s'),

        ], true);



        $infoModel->insert([

            'user_id' => $userId,

            'first_name' => "Quiel",

            'last_name' => "Caspe",

            'gender' => 'Male',

            'age' => rand(18, 70),

            'family_number' => rand(1, 4),

            'phone' => '09123456789',

            'purok' => 7,

            'barangay' => 'Borlongan',

            'municipality' => 'Dipaculao',

            'province' => 'Aurora',

            'zipcode' => '3203',

            'created_at' => date('Y-m-d H:i:s'),

            'updated_at' => date('Y-m-d H:i:s'),

        ]);



        // No billing added for this user

    }

}