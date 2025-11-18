<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuperadminActions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'action_type' => ['type' => 'VARCHAR', 'constraint' => 20], // create, delete
            'payload' => ['type' => 'TEXT', 'null' => true],
            'proposer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'approver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('superadmin_actions');
    }

    public function down()
    {
        $this->forge->dropTable('superadmin_actions');
    }
}
