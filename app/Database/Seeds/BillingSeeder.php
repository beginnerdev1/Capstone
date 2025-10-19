<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'bill_no'    => 'BILL-' . strtoupper(uniqid()),
                'user_id'    => 2,
                'amount_due' => 60,
                'due_date'   => '2025-11-01',
                'status'     => 'Unpaid',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'bill_no'    => 'BILL-' . strtoupper(uniqid()),
                'user_id'    => 3,
                'amount_due' => 75,
                'due_date'   => '2025-11-05',
                'status'     => 'Paid',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // ✅ Insert into DB
        $this->db->table('billings')->insertBatch($data);
    }
}
