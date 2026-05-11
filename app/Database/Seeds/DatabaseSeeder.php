<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductPriceSeeder::class);
        $this->call(QuickTemplateSeeder::class);
        $this->call(SaleLimitSettingSeeder::class);
        $this->call(SampleSalesSeeder::class);
    }
}
