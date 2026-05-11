<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php if ($isAdmin): ?>
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card card-soft stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small-muted">Total Transaksi</div>
                            <div class="summary-number">
                                <?= esc((string) $overallSummary['total_transactions']) ?>
                            </div>
                        </div>
                        <div class="icon-box bg-primary-subtle text-primary"><i class="bi bi-receipt"></i></div>
                    </div>
                    <div class="small-muted mt-3">Total keseluruhan transaksi yang tercatat.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-soft stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small-muted">Penjualan Hari Ini</div>
                            <div class="summary-number">
                                <?= format_rupiah($todaySummary['total_sales']) ?>
                            </div>
                        </div>
                        <div class="icon-box bg-success-subtle text-success"><i class="bi bi-calendar-check"></i></div>
                    </div>
                    <div class="small-muted mt-3">
                        <?= format_kg($todaySummary['total_kg']) ?> •
                        <?= esc((string) $todaySummary['total_transactions']) ?> transaksi
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-soft stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small-muted">Penjualan Bulan Ini</div>
                            <div class="summary-number">
                                <?= format_rupiah($monthSummary['total_sales']) ?>
                            </div>
                        </div>
                        <div class="icon-box bg-warning-subtle text-warning"><i class="bi bi-graph-up"></i></div>
                    </div>
                    <div class="small-muted mt-3">
                        <?= format_kg($monthSummary['total_kg']) ?> •
                        <?= esc((string) $monthSummary['total_transactions']) ?> transaksi
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-soft stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small-muted">Status Mode Limit</div>
                            <div class="summary-number fs-5">
                                <?= !empty($saleLimit['is_enabled']) ? 'Aktif' : 'Nonaktif' ?>
                            </div>
                        </div>
                        <div class="icon-box bg-danger-subtle text-danger"><i class="bi bi-shield-exclamation"></i></div>
                    </div>
                    <div class="small-muted mt-3">
                        <?= esc(sale_limit_text($saleLimit)) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="small-muted">Jumlah Pengguna Aktif</div>
                    <div class="summary-number">
                        <?= esc((string) $userCount) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="small-muted">Jumlah Produk Aktif</div>
                    <div class="summary-number">
                        <?= esc((string) $productCount) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="small-muted">Jumlah Template Aktif</div>
                    <div class="summary-number">
                        <?= esc((string) $templateCount) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-1">Grafik Penjualan Harian</h2>
                            <div class="small-muted">Ringkasan total penjualan 7 hari terakhir.</div>
                        </div>
                    </div>
                    <canvas id="dailySalesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-1">Grafik Penjualan Bulanan</h2>
                            <div class="small-muted">Perbandingan 6 bulan terakhir.</div>
                        </div>
                    </div>
                    <canvas id="monthlySalesChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-1">Transaksi Terbaru</h2>
                            <div class="small-muted">Riwayat transaksi terbaru yang tercatat di sistem.</div>
                        </div>
                        <a href="<?= site_url('/sales') ?>" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Pencatat</th>
                                    <th>Total KG</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentTransactions === []): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentTransactions as $transaction): ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                <?= esc($transaction['invoice_number']) ?>
                                            </td>
                                            <td>
                                                <?= esc(date('d-m-Y H:i', strtotime($transaction['transaction_date']))) ?>
                                            </td>
                                            <td>
                                                <?= esc($transaction['created_by_name'] ?? '-') ?>
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
        </div>
        <div class="col-xl-5">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-1">Template Transaksi Cepat</h2>
                            <div class="small-muted">Mempercepat input transaksi operasional.</div>
                        </div>
                        <a href="<?= site_url('/sales/create') ?>" class="btn btn-primary btn-sm">Transaksi Baru</a>
                    </div>

                    <?php if ($quickTemplates === []): ?>
                        <div class="alert alert-light border mb-0">Belum ada template transaksi aktif.</div>
                    <?php else: ?>
                        <div class="d-grid gap-3">
                            <?php foreach ($quickTemplates as $template): ?>
                                <div class="template-card p-3 bg-white">
                                    <div class="fw-semibold">
                                        <?= esc($template['template_name']) ?>
                                    </div>
                                    <div class="small-muted">
                                        <?= esc($template['template_code']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-xl-4 col-md-6">
            <a href="<?= site_url('/sales/create') ?>" class="text-decoration-none">
                <div class="card card-soft h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-box bg-primary-subtle text-primary"><i class="bi bi-cart-check"></i></div>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                        <h2 class="h4 mb-2 text-dark">Penjualan</h2>
                        <p class="page-subtitle mb-0">Input transaksi manual dengan qty beras 5 kg, 10 kg, dan 25 kg.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="<?= site_url('/sales/template') ?>" class="text-decoration-none">
                <div class="card card-soft h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-box bg-success-subtle text-success"><i class="bi bi-lightning-charge"></i>
                            </div>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                        <h2 class="h4 mb-2 text-dark">Template Cepat</h2>
                        <p class="page-subtitle mb-0">Pilih template aktif yang sudah dibuat admin untuk mempercepat
                            transaksi.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="<?= site_url('/sales') ?>" class="text-decoration-none">
                <div class="card card-soft h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-box bg-warning-subtle text-warning"><i class="bi bi-clock-history"></i></div>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                        <h2 class="h4 mb-2 text-dark">Riwayat Transaksi</h2>
                        <p class="page-subtitle mb-0">Lihat transaksi hari ini yang sudah berhasil dicatat pada sistem.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($isAdmin): ?>
    <script>
        const dailyChartData = <?= json_encode($dailyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const monthlyChartData = <?= json_encode($monthlyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

        const currencyFormat = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value || 0);

        new Chart(document.getElementById('dailySalesChart'), {
            type: 'line',
            data: {
                labels: dailyChartData.labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: dailyChartData.sales,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => currencyFormat(value)
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('monthlySalesChart'), {
            type: 'bar',
            data: {
                labels: monthlyChartData.labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
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
                        ticks: {
                            callback: (value) => currencyFormat(value)
                        }
                    }
                }
            }
        });
    </script>
<?php endif; ?>
<?= $this->endSection() ?>