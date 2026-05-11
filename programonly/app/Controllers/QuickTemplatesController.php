<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\QuickTemplateItemModel;
use App\Models\QuickTemplateModel;

class QuickTemplatesController extends BaseController
{
    private QuickTemplateModel $templateModel;
    private QuickTemplateItemModel $templateItemModel;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->templateModel = new QuickTemplateModel();
        $this->templateItemModel = new QuickTemplateItemModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $templates = $this->templateModel->getAllTemplates();
        $templateItems = $this->templateItemModel->getGroupedItemsByTemplateIds(array_column($templates, 'id'));

        return view('templates/index', [
            'title' => 'Kelola Template Transaksi',
            'templates' => $templates,
            'templateItems' => $templateItems,
        ]);
    }

    public function create()
    {
        $products = $this->productModel->getFixedPackagesWithCurrentPrice();

        return view('templates/form', [
            'title' => 'Tambah Template Transaksi',
            'template' => null,
            'items' => old('items') ?? $this->buildItemsFromTemplateQty(null, $products),
            'products' => array_values($products),
            'formAction' => '/admin/templates/store',
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();
        $normalized = $this->normalizeTemplatePayload($post);
        $errors = $this->validateTemplate($normalized);

        if ($this->templateModel->codeExists(trim((string) ($normalized['template_code'] ?? '')))) {
            $errors['template_code'] = 'Kode template sudah digunakan.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = db_connect();
        $db->transStart();

        $templateId = $this->templateModel->insert([
            'template_code' => trim((string) $normalized['template_code']),
            'template_name' => trim((string) $normalized['template_name']),
            'qty_5kg' => (int) $normalized['qty_5kg'],
            'qty_10kg' => (int) $normalized['qty_10kg'],
            'qty_25kg' => (int) $normalized['qty_25kg'],
            'is_active' => (int) ($normalized['is_active'] ?? 0),
            'created_by' => (int) current_user_id(),
        ], true);

        foreach ($this->buildTemplateItemsFromQty($normalized) as $item) {
            $item['template_id'] = $templateId;
            $this->templateItemModel->insert($item);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan template transaksi.');
        }

        return redirect()->to('/admin/templates')->with('success', 'Template transaksi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $template = $this->templateModel->find($id);

        if ($template === null) {
            return redirect()->to('/admin/templates')->with('error', 'Template tidak ditemukan.');
        }

        $products = $this->productModel->getFixedPackagesWithCurrentPrice();

        return view('templates/form', [
            'title' => 'Edit Template Transaksi',
            'template' => $template,
            'items' => old('items') ?? $this->buildItemsFromTemplateQty($template, $products),
            'products' => array_values($products),
            'formAction' => '/admin/templates/update/' . $id,
        ]);
    }

    public function update(int $id)
    {
        $template = $this->templateModel->find($id);

        if ($template === null) {
            return redirect()->to('/admin/templates')->with('error', 'Template tidak ditemukan.');
        }

        $post = $this->request->getPost();
        $normalized = $this->normalizeTemplatePayload($post);
        $errors = $this->validateTemplate($normalized);

        if ($this->templateModel->codeExists(trim((string) ($normalized['template_code'] ?? '')), $id)) {
            $errors['template_code'] = 'Kode template sudah digunakan.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $db = db_connect();
        $db->transStart();

        $this->templateModel->update($id, [
            'template_code' => trim((string) $normalized['template_code']),
            'template_name' => trim((string) $normalized['template_name']),
            'qty_5kg' => (int) $normalized['qty_5kg'],
            'qty_10kg' => (int) $normalized['qty_10kg'],
            'qty_25kg' => (int) $normalized['qty_25kg'],
            'is_active' => (int) ($normalized['is_active'] ?? 0),
        ]);

        $this->templateItemModel->where('template_id', $id)->delete();

        foreach ($this->buildTemplateItemsFromQty($normalized) as $item) {
            $item['template_id'] = $id;
            $this->templateItemModel->insert($item);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui template transaksi.');
        }

        return redirect()->to('/admin/templates')->with('success', 'Template transaksi berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $template = $this->templateModel->find($id);

        if ($template === null) {
            return redirect()->to('/admin/templates')->with('error', 'Template tidak ditemukan.');
        }

        $this->templateModel->delete($id);

        return redirect()->to('/admin/templates')->with('success', 'Template transaksi berhasil dihapus.');
    }

    private function validateTemplate(array $post): array
    {
        $rules = [
            'template_code' => 'required|max_length[50]|alpha_numeric_punct',
            'template_name' => 'required|max_length[150]',
            'qty_5kg' => 'required|integer|greater_than_equal_to[0]',
            'qty_10kg' => 'required|integer|greater_than_equal_to[0]',
            'qty_25kg' => 'required|integer|greater_than_equal_to[0]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validateData($post, $rules)) {
            return $this->validator->getErrors();
        }

        if (((int) $post['qty_5kg']) === 0 && ((int) $post['qty_10kg']) === 0 && ((int) $post['qty_25kg']) === 0) {
            return ['qty_5kg' => 'Minimal satu qty template harus lebih dari 0.'];
        }

        return [];
    }

    private function normalizeTemplatePayload(array $post): array
    {
        $normalized = [
            'template_code' => trim((string) ($post['template_code'] ?? '')),
            'template_name' => trim((string) ($post['template_name'] ?? '')),
            'qty_5kg' => (string) ($post['qty_5kg'] ?? '0'),
            'qty_10kg' => (string) ($post['qty_10kg'] ?? '0'),
            'qty_25kg' => (string) ($post['qty_25kg'] ?? '0'),
            'is_active' => (string) ($post['is_active'] ?? '1'),
        ];

        if (!isset($post['items']) || !is_array($post['items'])) {
            return $normalized;
        }

        $products = $this->productModel->getFixedPackagesWithCurrentPrice();
        $productWeightMap = [];

        foreach ($products as $weight => $product) {
            $productWeightMap[(int) $product['id']] = (int) $weight;
        }

        $qtyMap = [5 => 0, 10 => 0, 25 => 0];

        foreach ($post['items'] as $item) {
            $productId = isset($item['product_id']) ? (int) $item['product_id'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;

            if ($productId <= 0 || $quantity < 0) {
                continue;
            }

            $weight = $productWeightMap[$productId] ?? null;
            if ($weight !== null) {
                $qtyMap[$weight] += $quantity;
            }
        }

        $normalized['qty_5kg'] = (string) $qtyMap[5];
        $normalized['qty_10kg'] = (string) $qtyMap[10];
        $normalized['qty_25kg'] = (string) $qtyMap[25];

        return $normalized;
    }

    private function buildTemplateItemsFromQty(array $payload): array
    {
        $products = $this->productModel->getFixedPackagesWithCurrentPrice();
        $items = [];

        foreach ([5 => 'qty_5kg', 10 => 'qty_10kg', 25 => 'qty_25kg'] as $weight => $field) {
            $quantity = (int) ($payload[$field] ?? 0);
            if ($quantity <= 0 || !isset($products[$weight])) {
                continue;
            }

            $items[] = [
                'product_id' => (int) $products[$weight]['id'],
                'quantity' => $quantity,
            ];
        }

        return $items;
    }

    private function buildItemsFromTemplateQty(?array $template, array $products): array
    {
        $items = [];

        foreach ([5 => 'qty_5kg', 10 => 'qty_10kg', 25 => 'qty_25kg'] as $weight => $field) {
            if (!isset($products[$weight])) {
                continue;
            }

            $items[] = [
                'product_id' => (int) $products[$weight]['id'],
                'quantity' => (int) ($template[$field] ?? 0),
            ];
        }

        return $items;
    }
}
