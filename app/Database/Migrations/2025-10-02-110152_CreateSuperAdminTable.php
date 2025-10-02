<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuperAdminTable extends Migration
{
    public function up()
    {
         $this->forge->addField([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'admin_code' => [
            'type' => 'VARCHAR',
            'constraint' => 20,
            'unique' => true,
        ],
        'email' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'unique' => true,
        ],
        'password' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
        ],
        'role' => [
            'type' => 'ENUM("super_admin","admin")',
            'default' => 'admin',
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
    $this->forge->createTable('Super_admin');
    }

    public function down()
    {
        $this->forge->dropTable('Super_admin');
    }
}
