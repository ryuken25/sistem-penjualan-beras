<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SalesTransactionItemModel;
use App\Models\SalesTransactionModel;
use App\Models\UserModel;

class ReportsController extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $transactionModel = new SalesTransactionModel();
        $transactionItemModel = new SalesTransactionItemModel();
        $userModel = new UserModel();

        $filters = [
            'start_date' => trim((string) $this->request->getGet('start_date')),
            'end_date' => trim((string) $this->request->getGet('end_date')),
            'product_id' => trim((string) $this->request->getGet('product_id')),
            'recorded_by' => trim((string) $this->request->getGet('recorded_by')),
        ];

        $transactions = $transactionModel->getTransactions($filters);
        $items = $transactionItemModel->getGroupedItemsByTransactionIds(array_column($transactions, 'id'));

        $summary = [
            'total_transactions' => count($transactions),
            'total_kg' => 0,
            'total_sales' => 0,
        ];

        foreach ($transactions as $transaction) {
            $summary['total_kg'] += (float) $transaction['total_kg'];
            $summary['total_sales'] += (float) $transaction['grand_total'];
        }

        return view('reports/index', [
            'title' => 'Laporan Penjualan',
            'filters' => $filters,
            'transactions' => $transactions,
            'items' => $items,
            'summary' => $summary,
            'products' => $productModel->getActiveWithCurrentPrice(),
            'users' => $userModel->getActiveUsers(),
            'dailyChart' => $transactionModel->getDailyChart(14),
            'monthlyChart' => $transactionModel->getMonthlyChart(6),
        ]);
    }
}
