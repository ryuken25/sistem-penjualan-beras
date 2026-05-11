<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\QuickTemplateModel;
use App\Models\SaleLimitSettingModel;
use App\Models\SalesTransactionModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $transactionModel = new SalesTransactionModel();
        $templateModel = new QuickTemplateModel();
        $productModel = new ProductModel();
        $saleLimitModel = new SaleLimitSettingModel();
        $userModel = new UserModel();

        $isAdmin = is_admin();
        $userId = $isAdmin ? null : current_user_id();

        $data = [
            'title' => $isAdmin ? 'Dashboard Admin' : 'Dashboard Pegawai',
            'isAdmin' => $isAdmin,
            'overallSummary' => $transactionModel->getSummary($userId, 'all'),
            'todaySummary' => $transactionModel->getSummary($userId, 'today'),
            'monthSummary' => $transactionModel->getSummary($userId, 'month'),
            'dailyChart' => $transactionModel->getDailyChart(7, $userId),
            'monthlyChart' => $transactionModel->getMonthlyChart(6, $userId),
            'recentTransactions' => $transactionModel->getRecentTransactions(8, $userId),
            'saleLimit' => $saleLimitModel->getCurrentSetting(),
            'quickTemplates' => $templateModel->getActiveTemplates(),
        ];

        if ($isAdmin) {
            $data['userCount'] = $userModel->builder()->where('is_active', 1)->where('deleted_at', null)->countAllResults();
            $data['productCount'] = $productModel->builder()->where('is_active', 1)->where('deleted_at', null)->countAllResults();
            $data['templateCount'] = $templateModel->builder()->where('is_active', 1)->where('deleted_at', null)->countAllResults();
        }

        return view('dashboard/index', $data);
    }
}
