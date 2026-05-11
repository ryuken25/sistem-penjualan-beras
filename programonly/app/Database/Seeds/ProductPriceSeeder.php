<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProductPriceSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar');

        $this->db->table('product_prices')->insertBatch([
            [
                'id' => 1,
                'product_id' => 1,
                'price' => 78000,
                'effective_date' => $now->toDateString(),
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
            [
                'id' => 2,
                'product_id' => 2,
                'price' => 150000,
                'effective_date' => $now->toDateString(),
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
            [
                'id' => 3,
                'product_id' => 3,
                'price' => 360000,
                'effective_date' => $now->toDateString(),
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
        ]);
    }
}
