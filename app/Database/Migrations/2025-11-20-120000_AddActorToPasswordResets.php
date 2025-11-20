<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActorToPasswordResets extends Migration
{
    public function up()
    {
        $fields = [
            'actor' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'user',
                'null'       => false,
            ],
        ];

        $this->forge->addColumn('password_resets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('password_resets', 'actor');
    }
}
