<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescriptionToBillings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('billings', [
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'user_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('billings', 'description');
    }
}
