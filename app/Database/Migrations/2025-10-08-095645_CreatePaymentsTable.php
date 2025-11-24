<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [               // Added field
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'billing_id' => [            // Added field
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'payment_intent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'payment_method_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'method' => [
                'type'       => 'ENUM("gateway","manual","offline")',
                'default'    => 'gateway',
            ],
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'admin_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'receipt_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'amount' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'PHP',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending','paid','rejected','awaiting_payment','expired','cancelled','partial'],
                'default'    => 'pending',
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the payment session expires',
            ],
            'attempt_number' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'unsigned' => true,
                'default' => 1,
                'null' => false,
                'comment' => 'Payment attempt number for the day',
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'status', 'created_at'], false, 'idx_user_status_created');
        $this->forge->addKey(['status', 'expires_at'], false, 'idx_status_expires');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('billing_id', 'billings', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
