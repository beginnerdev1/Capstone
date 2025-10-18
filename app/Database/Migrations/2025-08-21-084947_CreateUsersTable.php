<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'Surname'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'Firstname'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'Middlename'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'username'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'Purok'    => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'Barangay'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'Municipality'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'Province'    => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'is_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'otp_code'    => [
                'type'       => 'VARCHAR',
                'constraint' => 6,
                'null'       => true,
            ],
            'otp_expires' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'created_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
