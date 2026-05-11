<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesTransactionItemModel extends Model
{
    protected $table = 'sales_transaction_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'transaction_id',
        'product_id',
        'product_name_snapshot',
        'weight_kg_snapshot',
        'unit_price_snapshot',
        'quantity',
        'subtotal',
        'total_kg_item',
    ];

    public function getItemsByTransaction(int $transactionId): array
    {
        return $this->where('transaction_id', $transactionId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getGroupedItemsByTransactionIds(array $transactionIds): array
    {
        if ($transactionIds === []) {
            return [];
        }

        $rows = $this->whereIn('transaction_id', $transactionIds)
            ->orderBy('id', 'ASC')
            ->findAll();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) $row['transaction_id']][] = $row;
        }

        return $grouped;
    }
}
