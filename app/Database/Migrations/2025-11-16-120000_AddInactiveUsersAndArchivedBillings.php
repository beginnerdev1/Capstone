<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInactiveUsersAndArchivedBillings extends Migration
{
    public function up()
    {
        // inactive_users table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'purok' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'barangay' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'municipality' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'province' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'zipcode' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'inactivated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'inactivated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inactive_users', true);

        // archived_billings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'inactive_ref_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'original_billing_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'bill_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'amount_due' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'billing_month' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'paid_date' => [
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
            'archived_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['inactive_ref_id','user_id']);
        $this->forge->addForeignKey('inactive_ref_id', 'inactive_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('archived_billings', true);
    }

    public function down()
    {
        $this->forge->dropTable('archived_billings', true);
        $this->forge->dropTable('inactive_users', true);
    }
}
