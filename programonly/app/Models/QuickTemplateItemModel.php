<?php

namespace App\Models;

use CodeIgniter\Model;

class QuickTemplateItemModel extends Model
{
    protected $table = 'quick_template_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'template_id',
        'product_id',
        'quantity',
    ];

    public function getItemsByTemplate(int $templateId): array
    {
        return $this->db->table($this->table . ' qti')
            ->select('qti.*, products.product_name, products.product_code, products.weight_kg, product_prices.price AS current_price')
            ->join('products', 'products.id = qti.product_id', 'left')
            ->join('product_prices', 'product_prices.product_id = products.id AND product_prices.is_current = 1', 'left')
            ->where('qti.template_id', $templateId)
            ->orderBy('products.weight_kg', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getGroupedItemsByTemplateIds(array $templateIds): array
    {
        if ($templateIds === []) {
            return [];
        }

        $rows = $this->db->table($this->table . ' qti')
            ->select('qti.*, products.product_name, products.product_code, products.weight_kg, products.is_active AS product_is_active, product_prices.price AS current_price')
            ->join('products', 'products.id = qti.product_id', 'left')
            ->join('product_prices', 'product_prices.product_id = products.id AND product_prices.is_current = 1', 'left')
            ->whereIn('qti.template_id', $templateIds)
            ->orderBy('qti.template_id', 'ASC')
            ->orderBy('products.weight_kg', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) $row['template_id']][] = $row;
        }

        return $grouped;
    }
}
