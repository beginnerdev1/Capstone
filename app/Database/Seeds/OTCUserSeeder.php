<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\BillingModel;

class OTCUserSeeder extends Seeder
{
    public function run()
    {
        $usersModel = new UsersModel();
        $infoModel = new UserInformationModel();
        $billingModel = new BillingModel();

        // Insert user
        $userId = $usersModel->insert([
            'email' => 'otcuser@mail.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'active' => 1,
            'status' => 'approved',
            'is_verified' => 1,
            'profile_complete' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], true); // returns inserted ID

        // Insert user info
        $infoModel->insert([
            'user_id' => $userId,
            'first_name' => 'OTC',
            'last_name' => 'User',
            'gender' => 'Male',
            'age' => 30,
            'family_number' => 3,
            'phone' => '09123456789',
            'purok' => 1, // numeric purok
            'barangay' => 'Borlongan',
            'municipality' => 'Dipaculao',
            'province' => 'Aurora',
            'zipcode' => '3203',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Insert pending billing for current month
        $billingModel->insert([
            'user_id' => $userId,
            'bill_no' => 'BILL-OTC-001',
            'amount_due' => 500.00,
            'status' => 'Pending',
            'billing_month' => date('Y-m'), // current month
            'due_date' => date('Y-m-d', strtotime('+5 days')),
            'remarks' => 'Awaiting over-the-counter payment',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
