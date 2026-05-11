<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class SalesTransactionModel extends Model
{
    protected $table = 'sales_transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'invoice_number',
        'transaction_date',
        'created_by',
        'template_id',
        'customer_name',
        'qty_5kg',
        'qty_10kg',
        'qty_25kg',
        'price_5kg',
        'price_10kg',
        'price_25kg',
        'subtotal_5kg',
        'subtotal_10kg',
        'subtotal_25kg',
        'total_items',
        'total_kg',
        'total_harga',
        'grand_total',
        'source_transaksi',
        'notes',
    ];

    public function getLatestInvoiceForDate(string $datePart): ?array
    {
        return $this->like('invoice_number', 'TRX-' . $datePart . '-', 'after')
            ->orderBy('invoice_number', 'DESC')
            ->first();
    }

    public function getSummary(?int $userId = null, string $period = 'all'): array
    {
        $builder = $this->builder();
        $builder->select('COUNT(*) AS total_transactions, COALESCE(SUM(total_items), 0) AS total_items, COALESCE(SUM(total_kg), 0) AS total_kg, COALESCE(SUM(grand_total), 0) AS total_sales', false);

        if ($userId !== null) {
            $builder->where('created_by', $userId);
        }

        $now = Time::now('Asia/Makassar');

        if ($period === 'today') {
            $builder->where('transaction_date >=', $now->toDateString() . ' 00:00:00')
                ->where('transaction_date <=', $now->toDateString() . ' 23:59:59');
        }

        if ($period === 'month') {
            $builder->where('transaction_date >=', $this->getMonthStartDateTime($now->toDateString()))
                ->where('transaction_date <=', $this->getMonthEndDateTime($now->toDateString()));
        }

        $row = $builder->get()->getRowArray() ?? [];

        return [
            'total_transactions' => (int) ($row['total_transactions'] ?? 0),
            'total_items' => (int) ($row['total_items'] ?? 0),
            'total_kg' => (float) ($row['total_kg'] ?? 0),
            'total_sales' => (float) ($row['total_sales'] ?? 0),
        ];
    }

    public function getRecentTransactions(int $limit = 10, ?int $userId = null): array
    {
        $builder = $this->baseTransactionBuilder();

        if ($userId !== null) {
            $builder->where('st.created_by', $userId);
        }

        return $builder->orderBy('st.transaction_date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getTransactions(array $filters = [], ?int $userId = null): array
    {
        $builder = $this->baseTransactionBuilder();
        $needsGroup = false;

        if ($userId !== null) {
            $builder->where('st.created_by', $userId);
        }

        if (!empty($filters['start_date'])) {
            $builder->where('st.transaction_date >=', $filters['start_date'] . ' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $builder->where('st.transaction_date <=', $filters['end_date'] . ' 23:59:59');
        }

        if (!empty($filters['recorded_by'])) {
            $builder->where('st.created_by', (int) $filters['recorded_by']);
        }

        if (!empty($filters['product_id'])) {
            $builder->join('sales_transaction_items sti', 'sti.transaction_id = st.id', 'left')
                ->where('sti.product_id', (int) $filters['product_id']);
            $needsGroup = true;
        }

        if ($needsGroup) {
            $builder->groupBy('st.id');
        }

        return $builder->orderBy('st.transaction_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getTransactionDetail(int $id, ?int $userId = null): ?array
    {
        $builder = $this->baseTransactionBuilder()->where('st.id', $id);

        if ($userId !== null) {
            $builder->where('st.created_by', $userId);
        }

        return $builder->get()->getRowArray();
    }

    public function getDailyChart(int $days = 7, ?int $userId = null): array
    {
        $anchorDate = $this->getLatestTransactionDate($userId) ?? Time::today('Asia/Makassar')->toDateString();
        $startDate = date('Y-m-d', strtotime('-' . max(0, $days - 1) . ' days', strtotime($anchorDate)));

        $builder = $this->builder();
        $builder->select('DATE(transaction_date) AS label_date, COALESCE(SUM(grand_total), 0) AS total_sales, COALESCE(SUM(total_kg), 0) AS total_kg', false)
            ->where('transaction_date >=', $startDate . ' 00:00:00')
            ->where('transaction_date <=', $anchorDate . ' 23:59:59')
            ->groupBy('DATE(transaction_date)', false)
            ->orderBy('DATE(transaction_date)', 'ASC', false);

        if ($userId !== null) {
            $builder->where('created_by', $userId);
        }

        $rows = $builder->get()->getResultArray();
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[$row['label_date']] = [
                'total_sales' => (float) $row['total_sales'],
                'total_kg' => (float) $row['total_kg'],
            ];
        }

        $labels = [];
        $sales = [];
        $kgs = [];

        for ($i = 0; $i < $days; $i++) {
            $currentDate = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($startDate)));
            $key = $currentDate;

            $labels[] = date('d M', strtotime($currentDate));
            $sales[] = $mapped[$key]['total_sales'] ?? 0;
            $kgs[] = $mapped[$key]['total_kg'] ?? 0;
        }

        return [
            'labels' => $labels,
            'sales' => $sales,
            'kg' => $kgs,
        ];
    }

    public function getMonthlyChart(int $months = 6, ?int $userId = null): array
    {
        $anchorDate = $this->getLatestTransactionDate($userId) ?? Time::today('Asia/Makassar')->toDateString();
        $currentMonth = date('Y-m-01', strtotime($anchorDate));
        $startMonth = date('Y-m-01', strtotime('-' . max(0, $months - 1) . ' months', strtotime($currentMonth)));
        $endMonth = date('Y-m-t', strtotime($currentMonth));

        $builder = $this->builder();
        $builder->select("DATE_FORMAT(transaction_date, '%Y-%m') AS label_month, COALESCE(SUM(grand_total), 0) AS total_sales, COALESCE(SUM(total_kg), 0) AS total_kg", false)
            ->where('transaction_date >=', $startMonth . ' 00:00:00')
            ->where('transaction_date <=', $endMonth . ' 23:59:59')
            ->groupBy("DATE_FORMAT(transaction_date, '%Y-%m')", false)
            ->orderBy("DATE_FORMAT(transaction_date, '%Y-%m')", 'ASC', false);

        if ($userId !== null) {
            $builder->where('created_by', $userId);
        }

        $rows = $builder->get()->getResultArray();
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[$row['label_month']] = [
                'total_sales' => (float) $row['total_sales'],
                'total_kg' => (float) $row['total_kg'],
            ];
        }

        $labels = [];
        $sales = [];
        $kgs = [];

        for ($i = 0; $i < $months; $i++) {
            $monthDate = date('Y-m-01', strtotime('+' . $i . ' months', strtotime($startMonth)));
            $key = date('Y-m', strtotime($monthDate));

            $labels[] = date('M Y', strtotime($monthDate));
            $sales[] = $mapped[$key]['total_sales'] ?? 0;
            $kgs[] = $mapped[$key]['total_kg'] ?? 0;
        }

        return [
            'labels' => $labels,
            'sales' => $sales,
            'kg' => $kgs,
        ];
    }

    private function baseTransactionBuilder()
    {
        return $this->db->table($this->table . ' st')
            ->select('st.*, users.full_name AS created_by_name, quick_templates.template_name')
            ->join('users', 'users.id = st.created_by', 'left')
            ->join('quick_templates', 'quick_templates.id = st.template_id', 'left');
    }

    private function getLatestTransactionDate(?int $userId = null): ?string
    {
        $builder = $this->builder();
        $builder->select('DATE(MAX(transaction_date)) AS latest_date', false);

        if ($userId !== null) {
            $builder->where('created_by', $userId);
        }

        $row = $builder->get()->getRowArray();

        return !empty($row['latest_date']) ? (string) $row['latest_date'] : null;
    }

    private function getMonthStartDateTime(string $date): string
    {
        return date('Y-m-01 00:00:00', strtotime($date));
    }

    private function getMonthEndDateTime(string $date): string
    {
        return date('Y-m-t 23:59:59', strtotime($date));
    }
}
