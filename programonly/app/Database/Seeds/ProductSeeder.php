<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar')->toDateTimeString();

        $this->db->table('products')->insertBatch([
            [
                'id' => 1,
                'product_code' => 'BRS-005',
                'product_name' => 'Beras Premium 5 Kg',
                'weight_kg' => 5,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'product_code' => 'BRS-010',
                'product_name' => 'Beras Premium 10 Kg',
                'weight_kg' => 10,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'product_code' => 'BRS-025',
                'product_name' => 'Beras Premium 25 Kg',
                'weight_kg' => 25,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
