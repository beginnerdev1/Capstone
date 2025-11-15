<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id'        => 1,
                'bill_no'        => 'BILL-001',
                'amount_due'     => 60.00,
                'status'         => 'Pending',
                'billing_month'  => 'September',
                'due_date'       => date('Y-m-d', strtotime('+7 days')),
                'remarks'        => 'Monthly water usage for September',
                'paid_date'      => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 1,
                'bill_no'        => 'BILL-002',
                'amount_due'     => 60.00,
                'status'         => 'Pending',
                'billing_month'  => 'October',
                'due_date'       => date('Y-m-d', strtotime('+30 days')),
                'remarks'        => 'Monthly water usage for October',
                'paid_date'      => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('billings')->insertBatch($data);
    }
}
