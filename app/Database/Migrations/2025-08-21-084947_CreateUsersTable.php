<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'unique'     => true,
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 1, // 2 = active, 1 = inactive 0=pending -1 = suspended 
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'inactive', 'suspended'],
                'default'    => 'pending',
            ],
            'is_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0, // 0 = not verified, 1 = verified
            ],
            'otp_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 6,
                'null'       => true,
            ],
            'otp_expires' => [
                'type' => 'DATETIME',
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

        // Primary key
        $this->forge->addKey('id', true);

        // Create the table
        $this->forge->createTable('users');
    }

    public function down()
    {
        // Drop the table if it exists
        $this->forge->dropTable('users', true);
    }
}
