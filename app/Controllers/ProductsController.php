<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductsController extends BaseController
{
    private ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        return view('products/index', [
            'title' => 'Kelola Produk Beras',
            'products' => $this->productModel->getAllWithCurrentPrice(),
        ]);
    }

    public function create()
    {
        return view('products/form', [
            'title' => 'Tambah Produk',
            'product' => null,
            'formAction' => '/admin/products/store',
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();
        $errors = $this->validateProduct($post);

        if ($this->productModel->codeExists(trim((string) ($post['product_code'] ?? '')))) {
            $errors['product_code'] = 'Kode produk sudah digunakan.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->productModel->insert([
            'product_code' => trim((string) $post['product_code']),
            'product_name' => trim((string) $post['product_name']),
            'weight_kg' => (float) $post['weight_kg'],
            'is_active' => (int) ($post['is_active'] ?? 0),
        ]);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = $this->productModel->find($id);

        if ($product === null) {
            return redirect()->to('/admin/products')->with('error', 'Data produk tidak ditemukan.');
        }

        return view('products/form', [
            'title' => 'Edit Produk',
            'product' => $product,
            'formAction' => '/admin/products/update/' . $id,
        ]);
    }

    public function update(int $id)
    {
        $product = $this->productModel->find($id);

        if ($product === null) {
            return redirect()->to('/admin/products')->with('error', 'Data produk tidak ditemukan.');
        }

        $post = $this->request->getPost();
        $errors = $this->validateProduct($post);

        if ($this->productModel->codeExists(trim((string) ($post['product_code'] ?? '')), $id)) {
            $errors['product_code'] = 'Kode produk sudah digunakan.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->productModel->update($id, [
            'product_code' => trim((string) $post['product_code']),
            'product_name' => trim((string) $post['product_name']),
            'weight_kg' => (float) $post['weight_kg'],
            'is_active' => (int) ($post['is_active'] ?? 0),
        ]);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $product = $this->productModel->find($id);

        if ($product === null) {
            return redirect()->to('/admin/products')->with('error', 'Data produk tidak ditemukan.');
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil dihapus.');
    }

    private function validateProduct(array $post): array
    {
        $rules = [
            'product_code' => 'required|max_length[50]|alpha_numeric_punct',
            'product_name' => 'required|max_length[150]',
            'weight_kg' => 'required|in_list[5,10,25,5.00,10.00,25.00]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validateData($post, $rules)) {
            return $this->validator->getErrors();
        }

        return [];
    }
}
