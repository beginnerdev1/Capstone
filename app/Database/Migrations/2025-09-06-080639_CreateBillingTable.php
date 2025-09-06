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
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', // supports 99999999.99
            ],
            'billing_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['paid', 'unpaid'],
                'default'    => 'unpaid',
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

        // Foreign key: restrict delete/update if user still has billings
        $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'RESTRICT');

        $this->forge->createTable('billings');
    }

    public function down()
    {
        $this->forge->dropTable('billings');
    }
}
