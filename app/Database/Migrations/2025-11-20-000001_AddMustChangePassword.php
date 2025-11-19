<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMustChangePassword extends Migration
{
    public function up()
    {
        // Add must_change_password flag to admin and super_admin tables
        $fields = [
            'must_change_password' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
            ],
        ];

        // Safe-add columns if tables exist
        if ($this->db->tableExists('admin')) {
            $this->forge->addColumn('admin', $fields);
        }
        if ($this->db->tableExists('super_admin')) {
            $this->forge->addColumn('super_admin', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('admin')) {
            $this->forge->dropColumn('admin', 'must_change_password');
        }
        if ($this->db->tableExists('super_admin')) {
            $this->forge->dropColumn('super_admin', 'must_change_password');
        }
    }
}
