<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTransactionItemsTable extends Migration
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
            'transaction_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'product_name_snapshot' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'weight_kg_snapshot' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'unit_price_snapshot' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'total_kg_item' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        $this->forge->addForeignKey('transaction_id', 'sales_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('sales_transaction_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('sales_transaction_items', true);
    }
}
