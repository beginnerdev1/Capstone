<?php
//php spark make:migration YourTableName
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServiceReportsTable extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'issue_type' => [
                'type'           => 'VARCHAR',
                'constraint'     => 100,
            ],
            'description' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['pending', 'in_progress', 'resolved'],
                'default'        => 'pending',
            ],
            'address' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'latitude' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,7',
                'null'           => true,
            ],
            'longitude' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,7',
                'null'           => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('service_reports');
    }

    public function down()
    {
        $this->forge->dropTable('service_reports');
    }
}
