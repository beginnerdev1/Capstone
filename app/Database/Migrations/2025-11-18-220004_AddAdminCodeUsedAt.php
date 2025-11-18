<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminCodeUsedAt extends Migration
{
    public function up()
    {
        $fields = [
            'admin_code_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('super_admin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('super_admin', ['admin_code_used_at']);
    }
}
