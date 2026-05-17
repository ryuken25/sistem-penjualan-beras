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

    public function getBasePriceHistory(): array
    {
        $productModel = new ProductModel();
        $packages = $productModel->getFixedPackagesWithCurrentPrice();

        if (!isset($packages[25])) {
            return [];
        }

        $baseProductId = (int) $packages[25]['id'];

        $rows = $this->select('product_prices.id, product_prices.price, product_prices.price_change, product_prices.is_current, product_prices.created_at')
            ->where('product_id', $baseProductId)
            ->orderBy('product_prices.created_at', 'DESC')
            ->orderBy('product_prices.id', 'DESC')
            ->findAll();

        $numbered = [];
        $i = 1;
        foreach ($rows as $row) {
            $row['row_number'] = $i++;
            $numbered[] = $row;
        }

        return $numbered;
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

    public function setBasePrice(float $base, int $updatedBy): array
    {
        if ($base <= 0) {
            throw new RuntimeException('Harga pokok harus lebih besar dari 0.');
        }

        $productModel = new ProductModel();
        $packages = $productModel->getFixedPackagesWithCurrentPrice();

        foreach ([5, 10, 25] as $weight) {
            if (!isset($packages[$weight])) {
                throw new RuntimeException('Data produk beras ' . $weight . ' kg belum tersedia.');
            }
        }

        $derived = derive_package_prices($base);
        $result = [];

        foreach ([5, 10, 25] as $weight) {
            $productId = (int) $packages[$weight]['id'];
            $newPrice = (float) $derived[$weight];
            $previous = (float) ($packages[$weight]['current_price'] ?? 0);

            $this->replaceCurrentPrice($productId, $newPrice, $updatedBy);

            $result[$weight] = [
                'product_id' => $productId,
                'previous_price' => $previous,
                'new_price' => $newPrice,
                'delta' => $newPrice - $previous,
            ];
        }

        return $result;
    }
}
