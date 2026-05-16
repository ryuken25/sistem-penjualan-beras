<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class ProductPriceModel extends Model
{
    protected $table = 'product_prices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'product_id',
        'price',
        'price_change',
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
        $previous = $this->getCurrentPrice($productId);
        $previousPrice = $previous !== null ? (float) $previous['price'] : 0.0;
        $delta = $previous !== null ? ($price - $previousPrice) : 0.0;

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
            'price_change' => $delta,
            'is_current' => 1,
            'updated_by' => $updatedBy,
        ]);
    }

    public function bulkAdjust(float $delta, int $updatedBy): array
    {
        if ($delta === 0.0) {
            throw new RuntimeException('Nilai penyesuaian tidak boleh 0.');
        }

        $productModel = new ProductModel();
        $packages = $productModel->getFixedPackagesWithCurrentPrice();
        $updated = [];

        foreach ([5, 10, 25] as $weight) {
            if (!isset($packages[$weight])) {
                throw new RuntimeException('Data produk beras ' . $weight . ' kg belum tersedia.');
            }

            $current = $packages[$weight]['current_price'];
            if ($current === null) {
                throw new RuntimeException('Harga aktif untuk beras ' . $weight . ' kg belum diatur. Atur harga awal terlebih dahulu.');
            }

            $newPrice = (float) $current + $delta;
            if ($newPrice <= 0) {
                throw new RuntimeException('Penyesuaian membuat harga beras ' . $weight . ' kg menjadi nol atau negatif.');
            }

            $this->replaceCurrentPrice((int) $packages[$weight]['id'], $newPrice, $updatedBy);

            $updated[$weight] = [
                'product_id' => (int) $packages[$weight]['id'],
                'previous_price' => (float) $current,
                'new_price' => $newPrice,
                'delta' => $delta,
            ];
        }

        return $updated;
    }
}
