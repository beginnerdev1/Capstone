<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminArchivedTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'original_admin_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'is_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'profile_picture' => [
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
            'archived_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('original_admin_id');
        $this->forge->createTable('admin_archived');
    }

    public function down()
    {
        $this->forge->dropTable('admin_archived');
    }
}
