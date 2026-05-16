<?php

namespace App\Controllers;

use App\Libraries\SaleTransactionService;
use App\Models\ProductModel;
use App\Models\QuickTemplateItemModel;
use App\Models\QuickTemplateModel;
use App\Models\SaleLimitSettingModel;
use App\Models\SalesTransactionItemModel;
use App\Models\SalesTransactionModel;
use RuntimeException;

class SalesController extends BaseController
{
    private SalesTransactionModel $salesTransactionModel;
    private SalesTransactionItemModel $salesTransactionItemModel;
    private ProductModel $productModel;
    private QuickTemplateModel $quickTemplateModel;
    private QuickTemplateItemModel $quickTemplateItemModel;
    private SaleLimitSettingModel $saleLimitSettingModel;
    private SaleTransactionService $saleTransactionService;

    public function __construct()
    {
        $this->salesTransactionModel = new SalesTransactionModel();
        $this->salesTransactionItemModel = new SalesTransactionItemModel();
        $this->productModel = new ProductModel();
        $this->quickTemplateModel = new QuickTemplateModel();
        $this->quickTemplateItemModel = new QuickTemplateItemModel();
        $this->saleLimitSettingModel = new SaleLimitSettingModel();
        $this->saleTransactionService = new SaleTransactionService();
    }

    public function index()
    {
        $filters = [];
        $userId = null;

        if (!is_admin()) {
            $today = date('Y-m-d');
            $filters['start_date'] = $today;
            $filters['end_date'] = $today;
            $userId = current_user_id();
        }

        $transactions = $this->salesTransactionModel->getTransactions($filters, $userId);
        $items = $this->salesTransactionItemModel->getGroupedItemsByTransactionIds(array_column($transactions, 'id'));

        return view('sales/index', [
            'title' => 'Riwayat Transaksi',
            'transactions' => $transactions,
            'items' => $items,
            'isAdmin' => is_admin(),
            'showTodayOnlyNotice' => !is_admin(),
        ]);
    }

    public function create()
    {
        $templates = $this->quickTemplateModel->getActiveTemplates();
        $templateItems = $this->quickTemplateItemModel->getGroupedItemsByTemplateIds(array_column($templates, 'id'));
        $products = array_values($this->productModel->getFixedPackagesWithCurrentPrice());

        return view('sales/create', [
            'title' => 'Input Transaksi Penjualan',
            'products' => $products,
            'templates' => $templates,
            'templateItems' => $templateItems,
            'saleLimit' => $this->saleLimitSettingModel->getCurrentSetting(),
            'oldItems' => old('items') ?? [],
            'transactionMode' => 'manual',
        ]);
    }

    public function template()
    {
        $templates = $this->quickTemplateModel->getActiveTemplates();
        $templateItems = $this->quickTemplateItemModel->getGroupedItemsByTemplateIds(array_column($templates, 'id'));
        $products = array_values($this->productModel->getFixedPackagesWithCurrentPrice());

        return view('sales/create', [
            'title' => 'Template Cepat',
            'products' => $products,
            'templates' => $templates,
            'templateItems' => $templateItems,
            'saleLimit' => $this->saleLimitSettingModel->getCurrentSetting(),
            'oldItems' => old('items') ?? [],
            'transactionMode' => 'template',
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if ((string) ($post['is_confirmed'] ?? '0') !== '1') {
            return redirect()->back()->withInput()->with('error', 'Transaksi harus dikonfirmasi terlebih dahulu sebelum disimpan.');
        }

        $post['transaction_date'] = date('Y-m-d H:i:s');

        try {
            $transaction = $this->saleTransactionService->createTransaction($post, (int) current_user_id());
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->to('/sales/invoice/' . $transaction['transaction_id'])
            ->with('success', 'Transaksi berhasil disimpan dengan nomor ' . $transaction['transaction']['invoice_number'] . '.');
    }

    public function invoice(int $id)
    {
        $userId = is_admin() ? null : current_user_id();
        $transaction = $this->salesTransactionModel->getTransactionDetail($id, $userId);

        if ($transaction === null) {
            return redirect()->to('/sales')->with('error', 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $items = $this->salesTransactionItemModel->getItemsByTransaction($id);

        return view('sales/invoice', [
            'transaction' => $transaction,
            'items' => $items,
        ]);
    }
}
