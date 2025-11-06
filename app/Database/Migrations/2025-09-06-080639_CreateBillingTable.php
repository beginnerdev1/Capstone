<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'bill_no' => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
                'unique'         => true,
            ],
            'amount_due' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'default'        => 60.00,
            ],
            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Pending', 'Paid', 'Rejected', 'Over the Counter'],
                'default'        => 'Pending',
            ],
            'billing_month' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
            ],
            'due_date' => [
                'type'           => 'DATE',
                'null'           => true,
            ],
            'paid_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'proof_of_payment' => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],
            'remarks' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('billings');
    }

    public function down()
    {
        $this->forge->dropTable('billings');
    }
}