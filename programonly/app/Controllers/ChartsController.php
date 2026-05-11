<?php

namespace App\Controllers;

use App\Models\SalesTransactionModel;

class ChartsController extends BaseController
{
    public function index()
    {
        $transactionModel = new SalesTransactionModel();

        return view('charts/index', [
            'title' => 'Grafik Penjualan',
            'dailyChart' => $transactionModel->getDailyChart(14),
            'monthlyChart' => $transactionModel->getMonthlyChart(6),
            'yearlyChart' => $transactionModel->getMonthlyChart(12),
        ]);
    }
}
