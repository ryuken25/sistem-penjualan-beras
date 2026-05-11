<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="card card-soft mb-4">
    <div class="card-body">
        <form method="get" action="<?= site_url('/admin/reports') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-control"
                        value="<?= esc($filters['start_date']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Produk</label>
                    <select name="product_id" class="form-select">
                        <option value="">Semua Produk</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= esc((string) $product['id']) ?>" <?= (string) $filters['product_id'] === (string) $product['id'] ? 'selected' : '' ?>>
                                <?= esc($product['product_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pencatat Transaksi</label>
                    <select name="recorded_by" class="form-select">
                        <option value="">Semua User</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= esc((string) $user['id']) ?>" <?= (string) $filters['recorded_by'] === (string) $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="<?= site_url('/admin/reports') ?>" class="btn btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="small-muted">Total Transaksi</div>
                <div class="summary-number">
                    <?= esc((string) $summary['total_transactions']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="small-muted">Total Kilogram</div>
                <div class="summary-number">
                    <?= format_kg($summary['total_kg']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="small-muted">Total Pendapatan</div>
                <div class="summary-number">
                    <?= format_rupiah($summary['total_sales']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Grafik Penjualan Harian</h2>
                <canvas id="reportDailyChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Grafik Penjualan Bulanan</h2>
                <canvas id="reportMonthlyChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pencatat</th>
                        <th>Template</th>
                        <th>Detail Item</th>
                        <th>Total KG</th>
                        <th>Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions === []): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data transaksi pada filter saat
                                ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php $detailItems = $items[$transaction['id']] ?? []; ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?= esc($transaction['invoice_number']) ?>
                                </td>
                                <td>
                                    <?= esc(date('d-m-Y H:i', strtotime((string) $transaction['transaction_date']))) ?>
                                </td>
                                <td>
                                    <?= esc($transaction['created_by_name'] ?? '-') ?>
                                </td>
                                <td>
                                    <?= esc($transaction['template_name'] ?? '-') ?>
                                </td>
                                <td>
                                    <ul class="transaction-item-list ps-3 mb-0">
                                        <?php foreach ($detailItems as $item): ?>
                                            <li>
                                                <?= esc($item['product_name_snapshot']) ?> ×
                                                <?= esc((string) $item['quantity']) ?>
                                                <span class="small-muted">(
                                                    <?= format_rupiah($item['subtotal']) ?>)
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td>
                                    <?= format_kg($transaction['total_kg']) ?>
                                </td>
                                <td>
                                    <?= format_rupiah($transaction['grand_total']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dailyChartData = <?= json_encode($dailyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const monthlyChartData = <?= json_encode($monthlyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    const currencyFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value || 0);

    new Chart(document.getElementById('reportDailyChart'), {
        type: 'line',
        data: {
            labels: dailyChartData.labels,
            datasets: [{
                label: 'Penjualan',
                data: dailyChartData.sales,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: { callback: (value) => currencyFormat(value) }
                }
            }
        }
    });

    new Chart(document.getElementById('reportMonthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyChartData.labels,
            datasets: [{
                label: 'Penjualan',
                data: monthlyChartData.sales,
                backgroundColor: '#0f766e',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: { callback: (value) => currencyFormat(value) }
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>