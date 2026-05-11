<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'product_code',
        'product_name',
        'weight_kg',
        'is_active',
    ];

    public function getAllWithCurrentPrice(bool $onlyActive = false): array
    {
        $builder = $this->builder();
        $builder->select('products.*, product_prices.price AS current_price, product_prices.effective_date AS price_effective_date')
            ->join('product_prices', 'product_prices.product_id = products.id AND product_prices.is_current = 1', 'left')
            ->where('products.deleted_at', null)
            ->orderBy('products.weight_kg', 'ASC')
            ->orderBy('products.product_name', 'ASC');

        if ($onlyActive) {
            $builder->where('products.is_active', 1);
        }

        return $builder->get()->getResultArray();
    }

    public function getActiveWithCurrentPrice(): array
    {
        return $this->getAllWithCurrentPrice(true);
    }

    public function getFixedPackagesWithCurrentPrice(): array
    {
        $rows = $this->builder()
            ->select('products.*, product_prices.price AS current_price, product_prices.effective_date AS price_effective_date')
            ->join('product_prices', 'product_prices.product_id = products.id AND product_prices.is_current = 1', 'left')
            ->where('products.deleted_at', null)
            ->where('products.is_active', 1)
            ->whereIn('products.weight_kg', [5, 10, 25])
            ->orderBy('products.weight_kg', 'ASC')
            ->get()
            ->getResultArray();

        $mapped = [];
        foreach ($rows as $row) {
            $mapped[(int) $row['weight_kg']] = $row;
        }

        return $mapped;
    }

    public function getByIdsWithCurrentPrice(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $builder = $this->builder();
        $builder->select('products.*, product_prices.price AS current_price, product_prices.effective_date AS price_effective_date')
            ->join('product_prices', 'product_prices.product_id = products.id AND product_prices.is_current = 1', 'left')
            ->whereIn('products.id', $productIds)
            ->where('products.deleted_at', null)
            ->where('products.is_active', 1);

        return $builder->get()->getResultArray();
    }

    public function codeExists(string $productCode, ?int $excludeId = null): bool
    {
        $builder = $this->builder();
        $builder->where('product_code', $productCode);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
