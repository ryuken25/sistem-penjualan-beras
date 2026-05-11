<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuickTemplatesTable extends Migration
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
            'template_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'template_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_by' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('template_code');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('quick_templates', true);
    }

    public function down()
    {
        $this->forge->dropTable('quick_templates', true);
    }
}
