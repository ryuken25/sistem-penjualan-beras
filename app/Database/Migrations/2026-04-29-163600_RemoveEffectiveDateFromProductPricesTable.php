<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveEffectiveDateFromProductPricesTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('effective_date', 'product_prices')) {
            $this->forge->dropColumn('product_prices', 'effective_date');
        }
    }

    public function down()
    {
        if (!$this->db->fieldExists('effective_date', 'product_prices')) {
            $this->forge->addColumn('product_prices', [
                'effective_date' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'price',
                ],
            ]);
        }
    }
}
