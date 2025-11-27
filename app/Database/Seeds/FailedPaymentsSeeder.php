<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FailedPaymentsSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are users with id 1 and 2 from UsersSeeder
        // Use a base timestamp and increment per row so composite unique keys
        // (e.g. user_id + status + created_at) do not collide.
        $base = time();

        $rows = [
            // Gateway payments
            [
                'user_id' => 1,
                'payment_intent_id' => 'PI-GW-FAILED-001',
                'payment_method_id' => 'PM-GW-01',
                'method' => 'gateway',
                'reference_number' => 'GW-FAILED-001',
                'amount' => 150.00,
                'currency' => 'PHP',
                'status' => 'failed',
                'paid_at' => null,
            ],
            [
                'user_id' => 1,
                'payment_intent_id' => 'PI-GW-REJECTED-001',
                'payment_method_id' => 'PM-GW-02',
                'method' => 'gateway',
                'reference_number' => 'GW-REJ-001',
                'amount' => 200.00,
                'currency' => 'PHP',
                'status' => 'rejected',
                'paid_at' => null,
            ],
            [
                'user_id' => 2,
                'payment_intent_id' => 'PI-GW-CANCELLED-001',
                'payment_method_id' => 'PM-GW-03',
                'method' => 'gateway',
                'reference_number' => 'GW-CXL-001',
                'amount' => 120.00,
                'currency' => 'PHP',
                'status' => 'cancelled',
                'paid_at' => null,
            ],

            // Manual / GCash payments
            [
                'user_id' => 2,
                'payment_intent_id' => null,
                'payment_method_id' => null,
                'method' => 'manual',
                'reference_number' => 'MAN-INVALID-001',
                'amount' => 300.00,
                'currency' => 'PHP',
                'status' => 'invalid',
                'paid_at' => null,
            ],
            [
                'user_id' => 1,
                'payment_intent_id' => null,
                'payment_method_id' => null,
                'method' => 'manual',
                'reference_number' => 'MAN-FAILED-001',
                'amount' => 250.00,
                'currency' => 'PHP',
                'status' => 'failed',
                'paid_at' => null,
            ],
            [
                'user_id' => 2,
                'payment_intent_id' => null,
                'payment_method_id' => null,
                'method' => 'manual',
                'reference_number' => 'MAN-REJ-001',
                'amount' => 175.00,
                'currency' => 'PHP',
                'status' => 'rejected',
                'paid_at' => null,
            ],
        ];

        $data = [];
        foreach ($rows as $i => $row) {
            $ts = date('Y-m-d H:i:s', $base + $i);
            $row['created_at'] = $ts;
            $row['updated_at'] = $ts;
            $data[] = $row;
        }

        $this->db->table('payments')->insertBatch($data);
    }
}
