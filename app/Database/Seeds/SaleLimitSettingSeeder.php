<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SaleLimitSettingSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar')->toDateTimeString();

        $setting = [
            'id' => 1,
            'is_enabled' => 0,
            'max_total_kg' => 100,
            'updated_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $exists = $this->db->table('sale_limit_settings')->where('id', $setting['id'])->countAllResults() > 0;

        if ($exists) {
            $this->db->table('sale_limit_settings')->where('id', $setting['id'])->update($setting);
        } else {
            $this->db->table('sale_limit_settings')->insert($setting);
        }
    }
}
