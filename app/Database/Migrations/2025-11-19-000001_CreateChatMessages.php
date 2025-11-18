<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatMessages extends Migration
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
            'external_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'default' => null,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'admin_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'sender' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'null' => false,
                'default' => 'user',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('external_id');
        $this->forge->createTable('chat_messages');
    }

    public function down()
    {
        $this->forge->dropTable('chat_messages');
    }
}
