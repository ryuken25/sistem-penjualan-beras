<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProductPriceSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar');

        // Model harga: base price = harga per kg untuk Beras 25 kg.
        //   harga_25kg = base
        //   harga_10kg = base + PRICE_STEP_PER_KG
        //   harga_5kg  = base + (PRICE_STEP_PER_KG * 2)
        $prices = [
            [
                'id' => 1,
                'product_id' => 1, // Beras 5 Kg
                'price' => 14200,
                'price_change' => 0,
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
            [
                'id' => 2,
                'product_id' => 2, // Beras 10 Kg
                'price' => 14100,
                'price_change' => 0,
                'is_current' => 1,
                'updated_by' => 1,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
            [
                'id' => 3,
                'product_id' => 3, // Beras 25 Kg (harga pokok)
                'price' => 14000,
                'price_change' => 0,
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
