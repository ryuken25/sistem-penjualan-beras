<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar')->toDateTimeString();

        $products = [
            [
                'id' => 1,
                'product_code' => 'BRS-005',
                'product_name' => 'Beras 5 Kg',
                'weight_kg' => 5,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'product_code' => 'BRS-010',
                'product_name' => 'Beras 10 Kg',
                'weight_kg' => 10,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'product_code' => 'BRS-025',
                'product_name' => 'Beras 25 Kg',
                'weight_kg' => 25,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($products as $product) {
            $exists = $this->db->table('products')->where('id', $product['id'])->countAllResults() > 0;

            if ($exists) {
                $this->db->table('products')->where('id', $product['id'])->update($product);
            } else {
                $this->db->table('products')->insert($product);
            }
        }
    }
}
