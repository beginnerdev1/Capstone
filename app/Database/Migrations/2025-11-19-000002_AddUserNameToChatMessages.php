<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserNameToChatMessages extends Migration
{
    public function up()
    {
        $fields = [
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
            ],
        ];
        $this->forge->addColumn('chat_messages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_messages', 'user_name');
    }
}
