<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleLimitSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'is_enabled' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'max_total_kg' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('sale_limit_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('sale_limit_settings', true);
    }
}
