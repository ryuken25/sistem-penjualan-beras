<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProductPriceSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar');

        $prices = [
            [
                'id' => 1,
                'product_id' => 1,
                'price' => 78000,
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
            [
                'id' => 2,
                'product_id' => 2,
                'price' => 150000,
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
            [
                'id' => 3,
                'product_id' => 3,
                'price' => 360000,
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
        ];

        foreach ($prices as $price) {
            $exists = $this->db->table('product_prices')->where('id', $price['id'])->countAllResults() > 0;

            if ($exists) {
                $this->db->table('product_prices')->where('id', $price['id'])->update($price);
            } else {
                $this->db->table('product_prices')->insert($price);
            }
        }
    }
}
