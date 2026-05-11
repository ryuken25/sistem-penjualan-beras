<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Grafik Penjualan Harian</h2>
                <div class="small-muted mb-3">Total penjualan 14 hari terakhir.</div>
                <canvas id="chartDailyPage" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Grafik Penjualan 6 Bulan</h2>
                <div class="small-muted mb-3">Ringkasan penjualan bulanan.</div>
                <canvas id="chartMonthlyPage" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="card-body">
        <h2 class="h5 mb-3">Grafik Tren 12 Bulan</h2>
        <div class="small-muted mb-3">Visualisasi tren penjualan tahunan secara sederhana dan mudah dibaca.</div>
        <canvas id="chartYearlyPage" height="100"></canvas>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dailyChartData = <?= json_encode($dailyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const monthlyChartData = <?= json_encode($monthlyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const yearlyChartData = <?= json_encode($yearlyChart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    const currencyFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value || 0);

    new Chart(document.getElementById('chartDailyPage'), {
        type: 'line',
        data: {
            labels: dailyChartData.labels,
            datasets: [{
                label: 'Penjualan Harian',
                data: dailyChartData.sales,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                tension: 0.35,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => currencyFormat(value),
                    },
                },
            },
        },
    });

    new Chart(document.getElementById('chartMonthlyPage'), {
        type: 'bar',
        data: {
            labels: monthlyChartData.labels,
            datasets: [{
                label: 'Penjualan Bulanan',
                data: monthlyChartData.sales,
                backgroundColor: '#0f766e',
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => currencyFormat(value),
                    },
                },
            },
        },
    });

    new Chart(document.getElementById('chartYearlyPage'), {
        type: 'line',
        data: {
            labels: yearlyChartData.labels,
            datasets: [{
                label: 'Tren Penjualan 12 Bulan',
                data: yearlyChartData.sales,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.12)',
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => currencyFormat(value),
                    },
                },
            },
        },
    });
</script>
<?= $this->endSection() ?>