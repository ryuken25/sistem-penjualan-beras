<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class QuickTemplateSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar')->toDateTimeString();

        $templates = [
            1 => [
                'id' => 1,
                'template_code' => 'TPL-001',
                'template_name' => 'Paket Operasional Ringkas',
                'qty_5kg' => 2,
                'qty_10kg' => 1,
                'qty_25kg' => 0,
                'discount_percent' => 10.00,
                'is_active' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            2 => [
                'id' => 2,
                'template_code' => 'TPL-002',
                'template_name' => 'Paket Campuran Penjualan',
                'qty_5kg' => 0,
                'qty_10kg' => 2,
                'qty_25kg' => 1,
                'discount_percent' => 15.00,
                'is_active' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            3 => [
                'id' => 3,
                'template_code' => 'TPL-003',
                'template_name' => 'Paket Grosir Mini',
                'qty_5kg' => 0,
                'qty_10kg' => 0,
                'qty_25kg' => 2,
                'discount_percent' => 18.00,
                'is_active' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            4 => [
                'id' => 4,
                'template_code' => 'TPL-004',
                'template_name' => 'Paket Hemat Keluarga',
                'qty_5kg' => 4,
                'qty_10kg' => 2,
                'qty_25kg' => 0,
                'discount_percent' => 12.00,
                'is_active' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($templates as $id => $template) {
            $exists = $this->db->table('quick_templates')->where('id', $id)->countAllResults() > 0;

            if ($exists) {
                $this->db->table('quick_templates')->where('id', $id)->update($template);
            } else {
                $this->db->table('quick_templates')->insert($template);
            }
        }

        $this->db->table('quick_template_items')->whereIn('template_id', [1, 2, 3, 4])->delete();
        $this->db->table('quick_template_items')->insertBatch([
            ['template_id' => 1, 'product_id' => 1, 'quantity' => 2],
            ['template_id' => 1, 'product_id' => 2, 'quantity' => 1],
            ['template_id' => 2, 'product_id' => 2, 'quantity' => 2],
            ['template_id' => 2, 'product_id' => 3, 'quantity' => 1],
            ['template_id' => 3, 'product_id' => 3, 'quantity' => 2],
            ['template_id' => 4, 'product_id' => 1, 'quantity' => 4],
            ['template_id' => 4, 'product_id' => 2, 'quantity' => 2],
        ]);
    }
}
