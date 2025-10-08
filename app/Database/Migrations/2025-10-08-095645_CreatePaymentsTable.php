<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'payment_intent_id'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'payment_method_id'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'amount'               => ['type' => 'INT', 'unsigned' => true],
            'currency'             => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'PHP'],
            'status'               => ['type' => 'VARCHAR', 'constraint' => 50],
            'user_id'              => ['type' => 'INT', 'unsigned' => true],
            'paid_at'              => ['type' => 'DATETIME', 'null' => true],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments');
    }



    public function down()
    {
      $this->forge->dropTable('payments');

    }
}
