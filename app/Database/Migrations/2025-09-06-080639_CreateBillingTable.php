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
                'constraint'     => 50,
                'unique'         => true,
            ],
            'amount_due' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'default'        => 60.00,
            ],
            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['Pending', 'Paid'],
                'default'        => 'Pending',
            ],
            'billing_month' => [
                'type'           => 'VARCHAR',
                'constraint'     => 20,
            ],
            'due_date' => [
                'type'           => 'DATE',
                'null'           => true,
            ],
            'paid_date' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'remarks' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('billings');
    }

    public function down()
    {
        $this->forge->dropTable('billings');
    }
}
