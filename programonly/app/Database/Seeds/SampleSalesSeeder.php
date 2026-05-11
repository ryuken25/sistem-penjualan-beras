<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SampleSalesSeeder extends Seeder
{
    public function run()
    {
        $today = Time::today('Asia/Makassar');
        $dates = [
            $today->subDays(2)->setTime(9, 15, 0),
            $today->subDays(1)->setTime(10, 45, 0),
            $today->setTime(14, 20, 0),
        ];

        $transactions = [
            1 => [
                'id' => 1,
                'invoice_number' => 'TRX-' . $dates[0]->format('Ymd') . '-0001',
                'transaction_date' => $dates[0]->toDateTimeString(),
                'created_by' => 1,
                'template_id' => 1,
                'customer_name' => 'Pembeli Internal A',
                'total_items' => 3,
                'qty_5kg' => 2,
                'qty_10kg' => 1,
                'qty_25kg' => 0,
                'price_5kg' => 78000,
                'price_10kg' => 150000,
                'price_25kg' => 360000,
                'subtotal_5kg' => 156000,
                'subtotal_10kg' => 150000,
                'subtotal_25kg' => 0,
                'total_kg' => 20,
                'total_harga' => 306000,
                'grand_total' => 306000,
                'source_transaksi' => 'template',
                'notes' => 'Data contoh laporan.',
                'created_at' => $dates[0]->toDateTimeString(),
                'updated_at' => $dates[0]->toDateTimeString(),
            ],
            2 => [
                'id' => 2,
                'invoice_number' => 'TRX-' . $dates[1]->format('Ymd') . '-0001',
                'transaction_date' => $dates[1]->toDateTimeString(),
                'created_by' => 2,
                'template_id' => 2,
                'customer_name' => 'Pembeli Internal B',
                'total_items' => 3,
                'qty_5kg' => 0,
                'qty_10kg' => 2,
                'qty_25kg' => 1,
                'price_5kg' => 78000,
                'price_10kg' => 150000,
                'price_25kg' => 360000,
                'subtotal_5kg' => 0,
                'subtotal_10kg' => 300000,
                'subtotal_25kg' => 360000,
                'total_kg' => 45,
                'total_harga' => 660000,
                'grand_total' => 660000,
                'source_transaksi' => 'template',
                'notes' => 'Transaksi contoh pegawai.',
                'created_at' => $dates[1]->toDateTimeString(),
                'updated_at' => $dates[1]->toDateTimeString(),
            ],
            3 => [
                'id' => 3,
                'invoice_number' => 'TRX-' . $dates[2]->format('Ymd') . '-0001',
                'transaction_date' => $dates[2]->toDateTimeString(),
                'created_by' => 2,
                'template_id' => null,
                'customer_name' => 'Pembeli Internal C',
                'total_items' => 2,
                'qty_5kg' => 1,
                'qty_10kg' => 1,
                'qty_25kg' => 0,
                'price_5kg' => 78000,
                'price_10kg' => 150000,
                'price_25kg' => 360000,
                'subtotal_5kg' => 78000,
                'subtotal_10kg' => 150000,
                'subtotal_25kg' => 0,
                'total_kg' => 15,
                'total_harga' => 228000,
                'grand_total' => 228000,
                'source_transaksi' => 'manual',
                'notes' => 'Transaksi manual contoh.',
                'created_at' => $dates[2]->toDateTimeString(),
                'updated_at' => $dates[2]->toDateTimeString(),
            ],
        ];

        foreach ($transactions as $id => $transaction) {
            $exists = $this->db->table('sales_transactions')->where('id', $id)->countAllResults() > 0;
            if ($exists) {
                $this->db->table('sales_transactions')->where('id', $id)->update($transaction);
            } else {
                $this->db->table('sales_transactions')->insert($transaction);
            }
        }

        $this->db->table('sales_transaction_items')->whereIn('transaction_id', [1, 2, 3])->delete();
        $this->db->table('sales_transaction_items')->insertBatch([
            [
                'transaction_id' => 1,
                'product_id' => 1,
                'product_name_snapshot' => 'Beras Premium 5 Kg',
                'weight_kg_snapshot' => 5,
                'unit_price_snapshot' => 78000,
                'quantity' => 2,
                'subtotal' => 156000,
                'total_kg_item' => 10,
            ],
            [
                'transaction_id' => 1,
                'product_id' => 2,
                'product_name_snapshot' => 'Beras Premium 10 Kg',
                'weight_kg_snapshot' => 10,
                'unit_price_snapshot' => 150000,
                'quantity' => 1,
                'subtotal' => 150000,
                'total_kg_item' => 10,
            ],
            [
                'transaction_id' => 2,
                'product_id' => 2,
                'product_name_snapshot' => 'Beras Premium 10 Kg',
                'weight_kg_snapshot' => 10,
                'unit_price_snapshot' => 150000,
                'quantity' => 2,
                'subtotal' => 300000,
                'total_kg_item' => 20,
            ],
            [
                'transaction_id' => 2,
                'product_id' => 3,
                'product_name_snapshot' => 'Beras Premium 25 Kg',
                'weight_kg_snapshot' => 25,
                'unit_price_snapshot' => 360000,
                'quantity' => 1,
                'subtotal' => 360000,
                'total_kg_item' => 25,
            ],
            [
                'transaction_id' => 3,
                'product_id' => 1,
                'product_name_snapshot' => 'Beras Premium 5 Kg',
                'weight_kg_snapshot' => 5,
                'unit_price_snapshot' => 78000,
                'quantity' => 1,
                'subtotal' => 78000,
                'total_kg_item' => 5,
            ],
            [
                'transaction_id' => 3,
                'product_id' => 2,
                'product_name_snapshot' => 'Beras Premium 10 Kg',
                'weight_kg_snapshot' => 10,
                'unit_price_snapshot' => 150000,
                'quantity' => 1,
                'subtotal' => 150000,
                'total_kg_item' => 10,
            ],
        ]);
    }
}
