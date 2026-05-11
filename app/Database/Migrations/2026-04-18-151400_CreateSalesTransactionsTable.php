<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTransactionsTable extends Migration
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
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'transaction_date' => [
                'type' => 'DATETIME',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'total_items' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_kg' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => '0.00',
            ],
            'grand_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => '0.00',
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addUniqueKey('invoice_number');
        $this->forge->addKey('transaction_date');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('template_id', 'quick_templates', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('sales_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('sales_transactions', true);
    }
}
