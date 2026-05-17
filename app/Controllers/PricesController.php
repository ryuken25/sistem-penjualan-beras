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
        $packages = $this->productModel->getFixedPackagesWithCurrentPrice();
        $currentBase = isset($packages[25]) ? (float) ($packages[25]['current_price'] ?? 0) : 0.0;

        return view('prices/index', [
            'title' => 'Kelola Harga Beras',
            'packages' => $packages,
            'currentBase' => $currentBase,
            'derivedPreview' => derive_package_prices($currentBase),
            'history' => $this->productPriceModel->getBasePriceHistory(),
        ]);
    }

    public function setBasePrice()
    {
        $post = $this->request->getPost();
        $rules = [
            'base_price' => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validateData($post, $rules)) {
            return redirect()->to('/admin/prices')
                ->with('errors', $this->validator->getErrors());
        }

        $newBase = (float) $post['base_price'];
        $packages = $this->productModel->getFixedPackagesWithCurrentPrice();
        $currentBase = isset($packages[25]) ? (float) ($packages[25]['current_price'] ?? 0) : 0.0;

        if ($newBase === $currentBase) {
            return redirect()->to('/admin/prices')
                ->with('error', 'Harga pokok tidak berubah.');
        }

        try {
            $this->productPriceModel->setBasePrice($newBase, (int) current_user_id());
        } catch (RuntimeException $exception) {
            return redirect()->to('/admin/prices')->with('error', $exception->getMessage());
        }

        return redirect()->to('/admin/prices')
            ->with('success', 'Harga pokok berhasil diperbarui. Harga 10 kg dan 5 kg ikut menyesuaikan otomatis.');
    }
}
