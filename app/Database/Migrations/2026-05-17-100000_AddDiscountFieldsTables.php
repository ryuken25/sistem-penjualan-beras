<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountFieldsTables extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('discount_percent', 'quick_templates')) {
            $this->forge->addColumn('quick_templates', [
                'discount_percent' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => 0,
                    'null' => false,
                    'after' => 'qty_25kg',
                ],
            ]);
        }

        if (!$this->db->fieldExists('discount_percent', 'sales_transactions')) {
            $this->forge->addColumn('sales_transactions', [
                'discount_percent' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => 0,
                    'null' => false,
                    'after' => 'total_harga',
                ],
            ]);
        }

        if (!$this->db->fieldExists('discount_amount', 'sales_transactions')) {
            $this->forge->addColumn('sales_transactions', [
                'discount_amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '15,2',
                    'default' => 0,
                    'null' => false,
                    'after' => 'discount_percent',
                ],
            ]);
        }
    }

    public function down()
    {
        foreach (['discount_percent'] as $field) {
            if ($this->db->fieldExists($field, 'quick_templates')) {
                $this->forge->dropColumn('quick_templates', $field);
            }
        }

        foreach (['discount_amount', 'discount_percent'] as $field) {
            if ($this->db->fieldExists($field, 'sales_transactions')) {
                $this->forge->dropColumn('sales_transactions', $field);
            }
        }
    }
}
