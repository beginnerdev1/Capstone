<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminRecipientToChatMessages extends Migration
{
    public function up()
    {
        // Add nullable admin_recipient_id to support admin<->admin private messages
        $fields = [
            'admin_recipient_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->addColumn('chat_messages', $fields);
        // Add index for faster lookups
        $this->forge->addKey('admin_recipient_id');
    }

    public function down()
    {
        $this->forge->dropColumn('chat_messages', 'admin_recipient_id');
    }
}
