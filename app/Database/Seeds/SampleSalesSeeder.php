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
            (clone $today)->subDays(2)->setTime(9, 15, 0),
            (clone $today)->subDays(1)->setTime(10, 45, 0),
            (clone $today)->setTime(14, 20, 0),
        ];

        // Harga per kg (base price 25kg = 14000).
        $price5  = 14200;
        $price10 = 14100;
        $price25 = 14000;

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
                'price_5kg' => $price5,
                'price_10kg' => $price10,
                'price_25kg' => $price25,
                'subtotal_5kg' => 5 * 2 * $price5,   // 142000
                'subtotal_10kg' => 10 * 1 * $price10, // 141000
                'subtotal_25kg' => 0,
                'total_kg' => 20,
                'total_harga' => 283000,
                'grand_total' => 283000,
                'discount_percent' => 0,
                'discount_amount' => 0,
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
                'price_5kg' => $price5,
                'price_10kg' => $price10,
                'price_25kg' => $price25,
                'subtotal_5kg' => 0,
                'subtotal_10kg' => 10 * 2 * $price10, // 282000
                'subtotal_25kg' => 25 * 1 * $price25, // 350000
                'total_kg' => 45,
                'total_harga' => 632000,
                'grand_total' => 632000,
                'discount_percent' => 0,
                'discount_amount' => 0,
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
                'price_5kg' => $price5,
                'price_10kg' => $price10,
                'price_25kg' => $price25,
                'subtotal_5kg' => 5 * 1 * $price5,   // 71000
                'subtotal_10kg' => 10 * 1 * $price10, // 141000
                'subtotal_25kg' => 0,
                'total_kg' => 15,
                'total_harga' => 212000,
                'grand_total' => 212000,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'source_transaksi' => 'manual',
                'notes' => 'Transaksi manual contoh.',
                'created_at' => $dates[2]->toDateTimeString(),
                'updated_at' => $dates[2]->toDateTimeString(),
            ],
        ];

        $sampleNotes = array_values(array_filter(array_map(
            static fn(array $transaction): ?string => $transaction['notes'] ?? null,
            $transactions,
        )));

        $existingRows = $this->db->table('sales_transactions')
            ->select('id')
            ->whereIn('notes', $sampleNotes)
            ->get()
            ->getResultArray();

        $existingIds = array_map(static fn(array $row): int => (int) $row['id'], $existingRows);

        if ($existingIds !== []) {
            $this->db->table('sales_transaction_items')->whereIn('transaction_id', $existingIds)->delete();
            $this->db->table('sales_transactions')->whereIn('id', $existingIds)->delete();
        }

        $transactionIdMap = [];

        foreach ($transactions as $key => $transaction) {
            $data = $transaction;
            unset($data['id']);

            $this->db->table('sales_transactions')->insert($data);
            $transactionIdMap[$key] = (int) $this->db->insertID();
        }

        $this->db->table('sales_transaction_items')->insertBatch([
            [
                'transaction_id' => $transactionIdMap[1],
                'product_id' => 1,
                'product_name_snapshot' => 'Beras 5 Kg',
                'weight_kg_snapshot' => 5,
                'unit_price_snapshot' => $price5,
                'quantity' => 2,
                'subtotal' => 5 * 2 * $price5,
                'total_kg_item' => 10,
            ],
            [
                'transaction_id' => $transactionIdMap[1],
                'product_id' => 2,
                'product_name_snapshot' => 'Beras 10 Kg',
                'weight_kg_snapshot' => 10,
                'unit_price_snapshot' => $price10,
                'quantity' => 1,
                'subtotal' => 10 * 1 * $price10,
                'total_kg_item' => 10,
            ],
            [
                'transaction_id' => $transactionIdMap[2],
                'product_id' => 2,
                'product_name_snapshot' => 'Beras 10 Kg',
                'weight_kg_snapshot' => 10,
                'unit_price_snapshot' => $price10,
                'quantity' => 2,
                'subtotal' => 10 * 2 * $price10,
                'total_kg_item' => 20,
            ],
            [
                'transaction_id' => $transactionIdMap[2],
                'product_id' => 3,
                'product_name_snapshot' => 'Beras 25 Kg',
                'weight_kg_snapshot' => 25,
                'unit_price_snapshot' => $price25,
                'quantity' => 1,
                'subtotal' => 25 * 1 * $price25,
                'total_kg_item' => 25,
            ],
            [
                'transaction_id' => $transactionIdMap[3],
                'product_id' => 1,
                'product_name_snapshot' => 'Beras 5 Kg',
                'weight_kg_snapshot' => 5,
                'unit_price_snapshot' => $price5,
                'quantity' => 1,
                'subtotal' => 5 * 1 * $price5,
                'total_kg_item' => 5,
            ],
            [
                'transaction_id' => $transactionIdMap[3],
                'product_id' => 2,
                'product_name_snapshot' => 'Beras 10 Kg',
                'weight_kg_snapshot' => 10,
                'unit_price_snapshot' => $price10,
                'quantity' => 1,
                'subtotal' => 10 * 1 * $price10,
                'total_kg_item' => 10,
            ],
        ]);
    }
}
