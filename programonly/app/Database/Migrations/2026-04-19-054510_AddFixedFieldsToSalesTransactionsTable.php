<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFixedFieldsToSalesTransactionsTable extends Migration
{
    public function up()
    {
        $fields = [];

        $definitions = [
            'qty_5kg' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'customer_name'],
            'qty_10kg' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'qty_5kg'],
            'qty_25kg' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'qty_10kg'],
            'price_5kg' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'qty_25kg'],
            'price_10kg' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'price_5kg'],
            'price_25kg' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'price_10kg'],
            'subtotal_5kg' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'price_25kg'],
            'subtotal_10kg' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'subtotal_5kg'],
            'subtotal_25kg' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'subtotal_10kg'],
            'total_harga' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'after' => 'grand_total'],
            'source_transaksi' => ['type' => 'ENUM', 'constraint' => ['manual', 'template'], 'default' => 'manual', 'after' => 'total_harga'],
        ];

        foreach ($definitions as $field => $definition) {
            if (!$this->db->fieldExists($field, 'sales_transactions')) {
                $fields[$field] = $definition;
            }
        }

        if ($fields !== []) {
            $this->forge->addColumn('sales_transactions', $fields);
        }
    }

    public function down()
    {
        foreach (['qty_5kg', 'qty_10kg', 'qty_25kg', 'price_5kg', 'price_10kg', 'price_25kg', 'subtotal_5kg', 'subtotal_10kg', 'subtotal_25kg', 'total_harga', 'source_transaksi'] as $field) {
            if ($this->db->fieldExists($field, 'sales_transactions')) {
                $this->forge->dropColumn('sales_transactions', $field);
            }
        }
    }
}
