<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'payment_intent_id' => 'PI-1001',
                'payment_method_id' => 'PM-123',
                'method'            => 'gateway',
                'reference_number'  => 'GCASH-1001',
                'amount'            => 150,
                'currency'          => 'PHP',
                'status'            => 'completed',
                'user_id'           => 1,
                'paid_at'           => date('Y-m-d H:i:s'),
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('payments')->insertBatch($data);
    }
}
