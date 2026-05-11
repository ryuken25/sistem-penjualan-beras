<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductPriceModel extends Model
{
    protected $table = 'product_prices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'product_id',
        'price',
        'is_current',
        'updated_by',
    ];

    public function getCurrentPrice(int $productId): ?array
    {
        return $this->where('product_id', $productId)
            ->where('is_current', 1)
            ->first();
    }

    public function getCurrentPriceMap(array $productIds = []): array
    {
        $builder = $this->builder();
        $builder->select('product_id, price')
            ->where('is_current', 1);

        if ($productIds !== []) {
            $builder->whereIn('product_id', $productIds);
        }

        $rows = $builder->get()->getResultArray();
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[(int) $row['product_id']] = $row;
        }

        return $mapped;
    }

    public function getPriceHistory(): array
    {
        return $this->select('product_prices.*, products.product_name, products.product_code, products.weight_kg, users.full_name AS updated_by_name')
            ->join('products', 'products.id = product_prices.product_id', 'left')
            ->join('users', 'users.id = product_prices.updated_by', 'left')
            ->orderBy('product_prices.created_at', 'DESC')
            ->findAll();
    }

    public function replaceCurrentPrice(int $productId, float $price, int $updatedBy): void
    {
        $this->builder()
            ->where('product_id', $productId)
            ->where('is_current', 1)
            ->update([
                'is_current' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->insert([
            'product_id' => $productId,
            'price' => $price,
            'is_current' => 1,
            'updated_by' => $updatedBy,
        ]);
    }
}
