<?php

namespace App\Libraries;

use App\Models\ProductModel;
use App\Models\QuickTemplateModel;
use App\Models\SaleLimitSettingModel;
use App\Models\SalesTransactionItemModel;
use App\Models\SalesTransactionModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\I18n\Time;
use RuntimeException;

class SaleTransactionService
{
    private BaseConnection $db;
    private ProductModel $productModel;
    private SalesTransactionModel $salesTransactionModel;
    private SalesTransactionItemModel $salesTransactionItemModel;
    private SaleLimitSettingModel $saleLimitSettingModel;
    private QuickTemplateModel $quickTemplateModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->productModel = new ProductModel();
        $this->salesTransactionModel = new SalesTransactionModel();
        $this->salesTransactionItemModel = new SalesTransactionItemModel();
        $this->saleLimitSettingModel = new SaleLimitSettingModel();
        $this->quickTemplateModel = new QuickTemplateModel();
    }

    public function createTransaction(array $payload, int $createdBy): array
    {
        $transactionDate = $this->normalizeTransactionDate($payload['transaction_date'] ?? null);
        $templateId = !empty($payload['template_id']) ? (int) $payload['template_id'] : null;
        $source = (string) ($payload['source_transaksi'] ?? ($templateId !== null ? 'template' : 'manual'));
        $customerName = trim((string) ($payload['customer_name'] ?? ''));

        $template = null;
        $discountPercent = 0.0;

        if ($source === 'template' && $templateId !== null) {
            $template = $this->quickTemplateModel->find($templateId);
            if ($template === null) {
                throw new RuntimeException('Template tidak ditemukan.');
            }

            if ($customerName === '') {
                throw new RuntimeException('Nama pelanggan wajib diisi saat memakai template cepat.');
            }

            if ($this->salesTransactionModel->existsTemplateCustomer($templateId, $customerName)) {
                throw new RuntimeException("Pelanggan '" . $customerName . "' sudah pernah memakai template ini. Setiap pelanggan hanya boleh 1x per template.");
            }

            // Override qty dari template (jangan percaya input client untuk mode template)
            $payload['qty_5kg'] = (int) ($template['qty_5kg'] ?? 0);
            $payload['qty_10kg'] = (int) ($template['qty_10kg'] ?? 0);
            $payload['qty_25kg'] = (int) ($template['qty_25kg'] ?? 0);

            $discountPercent = (float) ($template['discount_percent'] ?? 0);
            if ($discountPercent < 0) {
                $discountPercent = 0.0;
            } elseif ($discountPercent > 100) {
                $discountPercent = 100.0;
            }
        }

        $packageData = $this->preparePackageTransaction($payload);
        $items = $packageData['items'];
        $totals = $packageData['totals'];
        $setting = $this->saleLimitSettingModel->getCurrentSetting();

        if ($setting !== null && (int) $setting['is_enabled'] === 1 && $totals['total_kg'] > (float) $setting['max_total_kg']) {
            throw new RuntimeException('Transaksi melebihi batas maksimum ' . format_kg($setting['max_total_kg']) . '.');
        }

        $gross = (float) $totals['grand_total'];
        $discountAmount = round($gross * ($discountPercent / 100), 2);
        $netTotal = round($gross - $discountAmount, 2);

        $invoiceNumber = $this->generateInvoiceNumber($transactionDate);

        $this->db->transStart();

        $transactionId = $this->salesTransactionModel->insert([
            'invoice_number' => $invoiceNumber,
            'transaction_date' => $transactionDate,
            'created_by' => $createdBy,
            'template_id' => $templateId,
            'customer_name' => $customerName !== '' ? $customerName : null,
            'qty_5kg' => $packageData['qty']['5'],
            'qty_10kg' => $packageData['qty']['10'],
            'qty_25kg' => $packageData['qty']['25'],
            'price_5kg' => $packageData['price']['5'],
            'price_10kg' => $packageData['price']['10'],
            'price_25kg' => $packageData['price']['25'],
            'subtotal_5kg' => $packageData['subtotal']['5'],
            'subtotal_10kg' => $packageData['subtotal']['10'],
            'subtotal_25kg' => $packageData['subtotal']['25'],
            'total_items' => $totals['total_items'],
            'total_kg' => $totals['total_kg'],
            'total_harga' => $netTotal,
            'grand_total' => $netTotal,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'source_transaksi' => $source,
            'notes' => trim((string) ($payload['notes'] ?? '')) ?: null,
        ], true);

        foreach ($items as $item) {
            $item['transaction_id'] = $transactionId;
            $this->salesTransactionItemModel->insert($item);
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw new RuntimeException('Gagal menyimpan transaksi ke database.');
        }

        return [
            'transaction_id' => (int) $transactionId,
            'transaction' => $this->salesTransactionModel->getTransactionDetail((int) $transactionId),
            'items' => $this->salesTransactionItemModel->getItemsByTransaction((int) $transactionId),
        ];
    }

    public function preparePackageTransaction(array $payload): array
    {
        $qty = $this->extractFixedQuantities($payload);
        if ($qty['5'] === 0 && $qty['10'] === 0 && $qty['25'] === 0) {
            throw new RuntimeException('Minimal satu jumlah harus diisi.');
        }

        $packages = $this->productModel->getFixedPackagesWithCurrentPrice();

        foreach ([5, 10, 25] as $weight) {
            if (!isset($packages[$weight])) {
                throw new RuntimeException('Data produk beras ' . $weight . ' kg belum tersedia.');
            }

            if ($packages[$weight]['current_price'] === null) {
                throw new RuntimeException('Harga aktif untuk beras ' . $weight . ' kg belum diatur admin.');
            }
        }

        $price = [
            '5' => (float) $packages[5]['current_price'],
            '10' => (float) $packages[10]['current_price'],
            '25' => (float) $packages[25]['current_price'],
        ];

        $subtotal = [
            '5'  => 5  * $qty['5']  * $price['5'],
            '10' => 10 * $qty['10'] * $price['10'],
            '25' => 25 * $qty['25'] * $price['25'],
        ];

        $items = [];
        foreach ([5, 10, 25] as $weight) {
            $key = (string) $weight;
            if ($qty[$key] <= 0) {
                continue;
            }

            $items[] = [
                'product_id' => (int) $packages[$weight]['id'],
                'product_name_snapshot' => $packages[$weight]['product_name'],
                'weight_kg_snapshot' => (float) $packages[$weight]['weight_kg'],
                'unit_price_snapshot' => $price[$key],
                'quantity' => $qty[$key],
                'subtotal' => $subtotal[$key],
                'total_kg_item' => $weight * $qty[$key],
            ];
        }

        return [
            'qty' => $qty,
            'price' => $price,
            'subtotal' => $subtotal,
            'items' => $items,
            'totals' => $this->calculateTotals($items),
        ];
    }

    public function calculateTotals(array $items): array
    {
        $totalItems = 0;
        $totalKg = 0.0;
        $grandTotal = 0.0;

        foreach ($items as $item) {
            $totalItems += (int) $item['quantity'];
            $totalKg += (float) $item['total_kg_item'];
            $grandTotal += (float) $item['subtotal'];
        }

        return [
            'total_items' => $totalItems,
            'total_kg' => $totalKg,
            'grand_total' => $grandTotal,
        ];
    }

    private function extractFixedQuantities(array $payload): array
    {
        if (array_key_exists('qty_5kg', $payload) || array_key_exists('qty_10kg', $payload) || array_key_exists('qty_25kg', $payload)) {
            return [
                '5' => $this->normalizeQtyValue($payload['qty_5kg'] ?? 0, 'Beras 5 kg'),
                '10' => $this->normalizeQtyValue($payload['qty_10kg'] ?? 0, 'Beras 10 kg'),
                '25' => $this->normalizeQtyValue($payload['qty_25kg'] ?? 0, 'Beras 25 kg'),
            ];
        }

        $qty = ['5' => 0, '10' => 0, '25' => 0];
        $items = $payload['items'] ?? [];
        if (!is_array($items)) {
            return $qty;
        }

        $products = $this->productModel->getFixedPackagesWithCurrentPrice();
        $productWeightMap = [];

        foreach ($products as $weight => $product) {
            $productWeightMap[(int) $product['id']] = (string) $weight;
        }

        foreach ($items as $item) {
            $productId = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            if ($productId <= 0) {
                continue;
            }

            $weightKey = $productWeightMap[$productId] ?? null;
            if ($weightKey === null) {
                continue;
            }

            $qty[$weightKey] += $this->normalizeQtyValue($item['quantity'] ?? 0, 'Beras ' . $weightKey . ' kg');
        }

        return $qty;
    }

    private function normalizeQtyValue(mixed $value, string $label): int
    {
        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return 0;
        }

        if (!preg_match('/^\d+$/', $stringValue)) {
            throw new RuntimeException('Jumlah untuk ' . $label . ' harus berupa angka bulat 0 atau lebih.');
        }

        return (int) $stringValue;
    }

    public function generateInvoiceNumber(string $transactionDate): string
    {
        $datePart = date('Ymd', strtotime($transactionDate));
        $prefix = 'TRX-' . $datePart . '-';
        $latest = $this->salesTransactionModel->getLatestInvoiceForDate($datePart);
        $number = 1;

        if ($latest !== null && preg_match('/(\d{4})$/', (string) $latest['invoice_number'], $matches) === 1) {
            $number = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    private function normalizeTransactionDate(?string $transactionDate): string
    {
        if ($transactionDate === null || trim($transactionDate) === '') {
            return Time::now('Asia/Makassar')->toDateTimeString();
        }

        $normalized = str_replace('T', ' ', trim($transactionDate));

        if (strlen($normalized) === 16) {
            $normalized .= ':00';
        }

        if (strtotime($normalized) === false) {
            throw new RuntimeException('Format tanggal transaksi tidak valid.');
        }

        return date('Y-m-d H:i:s', strtotime($normalized));
    }
}
