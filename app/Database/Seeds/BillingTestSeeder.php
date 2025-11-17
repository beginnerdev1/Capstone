<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\BillingModel;

class BillingTestSeeder extends Seeder
{
    public function run()
    {
        $billing = new BillingModel();

        // User you showed:
        // user_id = 1
        // Name: Quiel Caspe
        // Purok 6, age 21, etc.
        // We only need user_id for billing.

        $records = [
            [
                'user_id'       => 1,
                'bill_no'       => 'BILL-2025-10-001',
                'amount_due'    => 500,
                'status'        => 'Unpaid',
                'billing_month' => '2025-10',
                'due_date'      => '2025-10-25',
                'paid_date'     => null,
                'remarks'       => 'Generated for October 2025',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'       => 1,
                'bill_no'       => 'BILL-2025-12-001',
                'amount_due'    => 500,
                'status'        => 'Unpaid',
                'billing_month' => '2025-12',
                'due_date'      => '2025-12-25',
                'paid_date'     => null,
                'remarks'       => 'Generated for December 2025',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($records as $row) {
            // Skip if already inserted
            $exists = $billing
                ->where('user_id', $row['user_id'])
                ->where('billing_month', $row['billing_month'])
                ->first();

            if (!$exists) {
                $billing->insert($row);
            }
        }
    }
}
