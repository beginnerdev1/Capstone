<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $paid_date = '2025-12-05 10:30:00';
        $otc_paid_date = '2025-11-14 14:00:00';

        // ------------------------------------
        // USERS TABLE
        // ------------------------------------
        $user_data_3 = [
            'email'            => 'mark.dela.cruz@example.com',
            'password'         => password_hash('password123', PASSWORD_DEFAULT),
            'active'           => 1,
            'status'           => 'approved',
            'is_verified'      => 1,
            'profile_complete' => 1,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        $this->db->table('users')->insert($user_data_3);
        $user_id_3 = $this->db->insertID();

        $user_data_4 = [
            'email'            => 'anna.garcia@example.com',
            'password'         => password_hash('password123', PASSWORD_DEFAULT),
            'active'           => 1,
            'status'           => 'pending',
            'is_verified'      => 0,
            'profile_complete' => 0,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        $this->db->table('users')->insert($user_data_4);
        $user_id_4 = $this->db->insertID();

        // ------------------------------------
        // USER_INFORMATION TABLE
        // ------------------------------------
        $user_info_data = [
            [
                'user_id'    => $user_id_3,
                'first_name' => 'Mark',
                'last_name'  => 'Dela Cruz',
                'gender'     => 'Male',
                'age'        => 45,
                'family_number' => 5,
                'phone'      => '09171112222',
                'purok'      => '1',
                'barangay'   => 'Borlongan',
                'municipality' => 'Dipaculao',
                'province'   => 'Aurora',
                'zipcode'    => '3203',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id'    => $user_id_4,
                'first_name' => 'Anna',
                'last_name'  => 'Garcia',
                'gender'     => 'Female',
                'age'        => 28,
                'family_number' => 3,
                'phone'      => '09983334444',
                'purok'      => '5',
                'barangay'   => 'Borlongan',
                'municipality' => 'Dipaculao',
                'province'   => 'Aurora',
                'zipcode'    => '3203',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        $this->db->table('user_information')->insertBatch($user_info_data);

        // ------------------------------------
        // BILLINGS TABLE
        // ------------------------------------
        $billing_data_3 = [
            'user_id'       => $user_id_3,
            'description'   => 'Monthly water usage for November',
            'bill_no'       => 'BILL-003',
            'amount_due'    => 150.00,
            'status'        => 'Paid',
            'billing_month' => 'November',
            'due_date'      => '2025-12-18',
            'paid_date'     => $paid_date,
            'proof_of_payment' => 'uploads/payments/proof_003.jpg',
            'remarks'       => 'Payment received via GCash.',
            'created_at'    => $now,
            'updated_at'    => $now,
        ];
        $this->db->table('billings')->insert($billing_data_3);
        $billing_id_3 = $this->db->insertID();

        $billing_data_batch = [
            [
                'user_id'       => $user_id_4,
                'description'   => 'Monthly water usage for September',
                'bill_no'       => 'BILL-004',
                'amount_due'    => 85.50,
                'status'        => 'Pending',
                'billing_month' => 'September',
                'due_date'      => '2025-10-15',
                'paid_date'     => null,
                'remarks'       => 'Initial bill for new user.',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'user_id'       => $user_id_4,
                'description'   => 'Monthly water usage for October',
                'bill_no'       => 'BILL-005',
                'amount_due'    => 92.00,
                'status'        => 'Over the Counter',
                'billing_month' => 'October',
                'due_date'      => '2025-11-15',
                'paid_date'     => $otc_paid_date,
                'remarks'       => 'Cash payment at office.',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];
        $this->db->table('billings')->insertBatch($billing_data_batch);

        // ------------------------------------
        // PAYMENTS TABLE
        // ------------------------------------
        $payment_data = [
            // Paid manual GCash
            [
                'user_id'           => $user_id_3,
                'billing_id'        => $billing_id_3,
                'payment_intent_id' => 'PI-1002',
                'payment_method_id' => 'PM-456',
                'method'            => 'manual',
                'reference_number'  => 'GCASH-1002',
                'admin_reference'   => 'ADM-REF-12A',
                'receipt_image'     => 'uploads/receipts/receipt_1002.jpg',
                'amount'            => 150,
                'currency'          => 'PHP',
                'status'            => 'Paid',
                'paid_at'           => $paid_date,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            // Pending offline
            [
                'user_id'           => $user_id_4,
                'billing_id'        => null,
                'payment_intent_id' => 'PI-1003',
                'payment_method_id' => null,
                'method'            => 'offline',
                'reference_number'  => 'CASH-DEP-55B',
                'admin_reference'   => null,
                'receipt_image'     => null,
                'amount'            => 50,
                'currency'          => 'PHP',
                'status'            => 'Pending',
                'paid_at'           => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            // NEW: Pending manual GCash (for modal button)
            [
                'user_id'           => $user_id_4,
                'billing_id'        => null,
                'payment_intent_id' => 'PI-1004',
                'payment_method_id' => null,
                'method'            => 'manual',
                'reference_number'  => 'GCASH-1003',
                'admin_reference'   => null,
                'receipt_image'     => 'uploads/receipts/receipt_1003.jpg',
                'amount'            => 100,
                'currency'          => 'PHP',
                'status'            => 'Pending',
                'paid_at'           => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];
        $this->db->table('payments')->insertBatch($payment_data);
    }
}
