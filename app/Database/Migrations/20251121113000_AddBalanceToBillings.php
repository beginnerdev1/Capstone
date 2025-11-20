<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBalanceToBillings extends Migration
{
    public function up()
    {
        // Ensure table exists before attempting to alter
        if (! $this->db->tableExists('billings')) {
            return;
        }

        $fields = [
            'balance' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => false,
                'default' => 0,
                'after' => 'amount_due'
            ]
        ];

        $this->forge->addColumn('billings', $fields);
    }

    public function down()
    {
        if ($this->db->tableExists('billings')) {
            // Dropping a column is safe if it exists
            try {
                $this->forge->dropColumn('billings', 'balance');
            } catch (\Throwable $_) {
                // ignore failures during rollback
            }
        }
    }
}
