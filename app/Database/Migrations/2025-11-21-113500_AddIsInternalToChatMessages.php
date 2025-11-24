<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsInternalToChatMessages extends Migration
{
    public function up()
    {
        $fields = [
            'is_internal' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
                'after' => 'sender',
            ],
        ];
        $this->forge->addColumn('chat_messages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_messages', 'is_internal');
    }
}
