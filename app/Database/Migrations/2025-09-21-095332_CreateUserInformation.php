<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserInformation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'info_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'unique'     => true,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'gender' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'age' => [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => true,
            ],
            'family_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'line_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
            ],
            'purok' => [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => true,
            ],

            'barangay' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Borlongan',
            ],
            'municipality' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Dipaculao',
            ],
            'province' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Aurora',
            ],
            'zipcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => '3203',
            ],
            'profile_picture' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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

        $this->forge->addKey('info_id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_information');
    }

    public function down()
    {
        $this->forge->dropTable('user_information');
    }
}
