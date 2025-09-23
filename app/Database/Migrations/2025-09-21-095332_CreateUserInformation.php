<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserInformation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'    => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'phone'      => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'email'      => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'street'     => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'address'    => [
                'type' => 'TEXT',
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
        ]);

        // Make user_id the PRIMARY KEY
        $this->forge->addKey('user_id', true);

        // Foreign key links user_information.user_id to users.id
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        // Create the table
        $this->forge->createTable('user_information');
    }

    public function down()
    {
        $this->forge->dropTable('user_information');
    }
}
