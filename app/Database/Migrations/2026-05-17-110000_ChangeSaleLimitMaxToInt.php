<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeSaleLimitMaxToInt extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('sale_limit_settings', [
            'max_total_kg' => [
                'name' => 'max_total_kg',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('sale_limit_settings', [
            'max_total_kg' => [
                'name' => 'max_total_kg',
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
                'null' => false,
            ],
        ]);
    }
}
