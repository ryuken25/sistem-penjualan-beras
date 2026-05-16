<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceChangeToProductPricesTable extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('price_change', 'product_prices')) {
            $this->forge->addColumn('product_prices', [
                'price_change' => [
                    'type' => 'DECIMAL',
                    'constraint' => '15,2',
                    'default' => 0,
                    'null' => false,
                    'after' => 'price',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('price_change', 'product_prices')) {
            $this->forge->dropColumn('product_prices', 'price_change');
        }
    }
}
