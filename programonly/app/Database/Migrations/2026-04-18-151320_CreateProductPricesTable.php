<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductPricesTable extends Migration
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
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'effective_date' => [
                'type' => 'DATE',
            ],
            'is_current' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->addKey(['product_id', 'is_current']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('product_prices', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_prices', true);
    }
}
