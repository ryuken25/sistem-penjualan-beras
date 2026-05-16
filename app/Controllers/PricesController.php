<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductPriceModel;
use RuntimeException;

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
        ];

        if (!$this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->productPriceModel->replaceCurrentPrice(
            $productId,
            (float) $post['price'],
            (int) current_user_id()
        );

        return redirect()->to('/admin/prices')->with('success', 'Harga produk berhasil diperbarui.');
    }

    public function bulkAdjust()
    {
        $delta = trim((string) $this->request->getPost('delta'));

        if ($delta === '' || !preg_match('/^-?\d+(\.\d+)?$/', $delta)) {
            return redirect()->to('/admin/prices')
                ->with('error', 'Nilai penyesuaian harus berupa angka (boleh negatif). Contoh: 200 atau -100.');
        }

        $deltaValue = (float) $delta;

        try {
            $this->productPriceModel->bulkAdjust($deltaValue, (int) current_user_id());
        } catch (RuntimeException $exception) {
            return redirect()->to('/admin/prices')->with('error', $exception->getMessage());
        }

        $direction = $deltaValue > 0 ? 'dinaikkan' : 'diturunkan';
        $absDelta = number_format(abs($deltaValue), 0, ',', '.');

        return redirect()->to('/admin/prices')->with(
            'success',
            'Harga semua kemasan berhasil ' . $direction . ' Rp ' . $absDelta . '/kg.'
        );
    }
}
