<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminActivityLogs extends Migration
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
            'actor_type' => [ // admin or superadmin
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'actor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'route' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'resource' => [ // file/table/path
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'details' => [ // JSON/text
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'logged_out_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['actor_type', 'actor_id']);
        $this->forge->createTable('admin_activity_logs');
    }

    public function down()
    {
        $this->forge->dropTable('admin_activity_logs');
    }
}
