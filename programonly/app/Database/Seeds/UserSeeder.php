<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Makassar')->toDateTimeString();

        $this->db->table('users')->insertBatch([
            [
                'id' => 1,
                'full_name' => 'Administrator UD Tulus Sari Merta',
                'username' => 'admin',
                'password_hash' => password_hash('admin12345', PASSWORD_DEFAULT),
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'full_name' => 'Pegawai Penjualan',
                'username' => 'pegawai',
                'password_hash' => password_hash('pegawai12345', PASSWORD_DEFAULT),
                'role' => 'pegawai',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
