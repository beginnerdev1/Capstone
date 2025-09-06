<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaidDateToBillings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('billings', [
            'paid_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('billings', 'paid_date');
    }
}