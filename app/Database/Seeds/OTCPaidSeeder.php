<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\BillingModel;
use App\Models\PaymentsModel;

class OTCPaidSeeder extends Seeder
{
    public function run()
    {
        $usersModel = new UsersModel();
        $infoModel = new UserInformationModel();
        $billingModel = new BillingModel();
        $paymentsModel = new PaymentsModel();

        $email = 'otcpaid@mail.com';

        // Check if user exists
        $user = $usersModel->where('email', $email)->first();
        if ($user) {
            $userId = $user['id'];
        } else {
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
        }

        // Insert or skip user info
        if (!$infoModel->where('user_id', $userId)->first()) {
            $infoModel->insert([
                'user_id' => $userId,
                'first_name' => 'OTC',
                'last_name' => 'Paid',
                'gender' => 'Male',
                'age' => 30,
                'family_number' => 3,
                'phone' => '09123456789',
                'purok' => 1,
                'barangay' => 'Borlongan',
                'municipality' => 'Dipaculao',
                'province' => 'Aurora',
                'zipcode' => '3203',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Insert payment record
        $paymentId = $paymentsModel->insert([
            'user_id' => $userId,
            'billing_id' => null,
            'payment_intent_id' => uniqid('intent_'),
            'payment_method_id' => uniqid('method_'),
            'method' => 'counter',
            'reference_number' => strtoupper(uniqid('REF')),
            'admin_reference' => 'ADMIN-OTC-' . rand(1000, 9999),
            'amount' => 500,
            'currency' => 'PHP',
            'status' => 'Confirmed',
            'paid_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], true);

        // Insert billing linked to payment
        $billingModel->insert([
            'user_id' => $userId,
            'payment_id' => $paymentId,
            'bill_no' => 'BILL-OTC-PAID-' . uniqid(),
            'amount_due' => 500,
            'status' => 'Paid',
            'billing_month' => date('Y-m'),
            'due_date' => date('Y-m-d', strtotime('+5 days')),
            'paid_date' => date('Y-m-d'),
            'proof_of_payment' => 'receipt.jpg',
            'remarks' => 'Paid over-the-counter',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
