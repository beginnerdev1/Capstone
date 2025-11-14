<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UsersModel;
use App\Models\UserInformationModel;
use App\Models\BillingModel;
use App\Models\PaymentsModel;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $usersModel = new UsersModel();
        $infoModel = new UserInformationModel();
        $billingModel = new BillingModel();
        $paymentsModel = new PaymentsModel();

        $barangay = 'Borlongan';
        $municipality = 'Dipaculao';
        $province = 'Aurora';
        $zipcode = '3203';

        $currentMonth = date('Y-m'); // e.g., 2025-11

        for ($purok = 1; $purok <= 7; $purok++) {
            for ($i = 1; $i <= 5; $i++) {
                $email = "user{$purok}_{$i}@mail.com";

                if ($usersModel->where('email', $email)->first()) continue;

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
                    'first_name' => "First{$purok}{$i}",
                    'last_name' => "Last{$purok}{$i}",
                    'gender' => (rand(0, 1) ? 'Male' : 'Female'),
                    'age' => rand(18, 60),
                    'family_number' => rand(2, 6),
                    'phone' => '09' . rand(100000000, 999999999),
                    'purok' => $purok, // store as number
                    'barangay' => $barangay,
                    'municipality' => $municipality,
                    'province' => $province,
                    'zipcode' => $zipcode,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $billingAmount = 300 + rand(50, 200);

                // Decide if paid or pending
                $isPaid = rand(0, 1) === 1;

                // Billing record
                $billingModel->insert([
                    'user_id' => $userId,
                    'bill_no' => strtoupper(uniqid('BILL')),
                    'amount_due' => $billingAmount,
                    'status' => $isPaid ? 'Paid' : 'Pending',
                    'billing_month' => $currentMonth,
                    'due_date' => date('Y-m-d', strtotime('+5 days')),
                    'paid_date' => $isPaid ? date('Y-m-d') : null,
                    'remarks' => $isPaid ? 'Payment successful' : 'Awaiting payment',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $billingId = $billingModel->getInsertID();

                // Payment record
                if ($isPaid) {
                    $method = (rand(0, 1) ? 'gateway' : 'manual'); // Some manual, some gateway
                    $status = $method === 'gateway' ? 'Paid' : (rand(0, 1) ? 'Paid' : 'Pending'); // Manual may be pending

                    $paymentsModel->insert([
                        'user_id' => $userId,
                        'billing_id' => $billingId,
                        'payment_intent_id' => $method === 'gateway' ? uniqid('intent_') : null,
                        'payment_method_id' => $method === 'gateway' ? uniqid('method_') : null,
                        'method' => $method,
                        'reference_number' => strtoupper(uniqid('REF')),
                        'admin_reference' => $method === 'manual' ? 'CNT-' . date('YmdHis') . '-' . rand(100, 999) : null,
                        'amount' => $billingAmount,
                        'currency' => 'PHP',
                        'status' => $status,
                        'paid_at' => $status === 'Paid' ? date('Y-m-d H:i:s') : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'deleted_at' => null,
                    ]);
                }
            }
        }
    }
}
