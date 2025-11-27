<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OverdueBillingsSeeder extends Seeder
{
    public function run()
    {
        // Seed some overdue billings for testing (assumes users with id 1 and 2 exist)
        $now = time();

        $rows = [
            [
                'user_id' => 1,
                'bill_no' => 'BILL-OVR-001',
                'amount_due' => '60.00',
                'carryover' => '0.00',
                'balance' => '60.00',
                'status' => 'Unpaid',
                'billing_month' => date('Y-m-d', strtotime('-2 months')),
                'due_date' => date('Y-m-d', strtotime('-30 days')),
                'paid_date' => null,
                'remarks' => 'Overdue test - unpaid',
            ],
            [
                'user_id' => 2,
                'bill_no' => 'BILL-OVR-002',
                'amount_due' => '120.00',
                'carryover' => '20.00',
                'balance' => '140.00',
                'status' => 'Pending',
                'billing_month' => date('Y-m-d', strtotime('-1 month')),
                'due_date' => date('Y-m-d', strtotime('-15 days')),
                'paid_date' => null,
                'remarks' => 'Overdue test - pending',
            ],
            [
                'user_id' => 1,
                'bill_no' => 'BILL-OVR-003',
                'amount_due' => '100.00',
                'carryover' => '30.00',
                'balance' => '70.00',
                'status' => 'Partial',
                'billing_month' => date('Y-m-d', strtotime('-3 months')),
                'due_date' => date('Y-m-d', strtotime('-40 days')),
                'paid_date' => null,
                'remarks' => 'Overdue test - partial',
            ],
        ];

        // Remove any existing rows with the same bill_no to make seeder idempotent
        $billNos = array_column($rows, 'bill_no');
        $this->db->table('billings')->whereIn('bill_no', $billNos)->delete();

        $data = [];
        foreach ($rows as $i => $r) {
            $ts = date('Y-m-d H:i:s', $now + $i);
            $r['created_at'] = $ts;
            $r['updated_at'] = $ts;
            $data[] = $r;
        }

        $this->db->table('billings')->insertBatch($data);
    }
}
