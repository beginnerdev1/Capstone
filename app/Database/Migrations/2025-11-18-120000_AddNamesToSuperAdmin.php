<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNamesToSuperAdmin extends Migration
{
    public function up()
    {
        $fields = [
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => null,
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => null,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->addColumn('super_admin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('super_admin', ['first_name','middle_name','last_name']);
    }
}
