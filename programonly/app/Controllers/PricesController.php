<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductPriceModel;

class PricesController extends BaseController
{
    private ProductModel $productModel;
    private ProductPriceModel $productPriceModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productPriceModel = new ProductPriceModel();
    }

    public function index()
    {
        return view('prices/index', [
            'title' => 'Kelola Harga Beras',
            'products' => $this->productModel->getAllWithCurrentPrice(),
            'priceHistory' => $this->productPriceModel->getPriceHistory(),
        ]);
    }

    public function update(int $productId)
    {
        $product = $this->productModel->find($productId);

        if ($product === null) {
            return redirect()->to('/admin/prices')->with('error', 'Produk tidak ditemukan.');
        }

        $post = $this->request->getPost();
        $rules = [
            'price' => 'required|decimal|greater_than[0]',
            'effective_date' => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->productPriceModel->replaceCurrentPrice(
            $productId,
            (float) $post['price'],
            (int) current_user_id(),
            (string) $post['effective_date']
        );

        return redirect()->to('/admin/prices')->with('success', 'Harga produk berhasil diperbarui.');
    }
}
