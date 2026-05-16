<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$packageMap = [];
foreach ($products as $product) {
    $weightKey = (int) $product['weight_kg'];
    if (in_array($weightKey, [5, 10, 25], true)) {
        $packageMap[$weightKey] = $product;
    }
}
?>

<div class="card card-soft mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Penyesuaian Harga Massal</h2>
                <div class="small-muted">Naikkan atau turunkan harga seluruh kemasan (5 kg, 10 kg, 25 kg) sekaligus per kg.</div>
            </div>
        </div>

        <form action="<?= site_url('/admin/prices/bulk-adjust') ?>" method="post" id="bulkAdjustForm">
            <?= csrf_field() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="bulkDelta">Perubahan Harga (Rp / kg)</label>
                    <input type="number" step="0.01" class="form-control" name="delta" id="bulkDelta"
                        placeholder="Contoh: 200 atau -100" required>
                    <div class="form-text">Isi <strong>200</strong> untuk menaikkan Rp 200/kg, atau <strong>-100</strong> untuk menurunkan Rp 100/kg.</div>
                </div>
                <div class="col-md-8">
                    <div class="small-muted mb-2">Pratinjau Harga Baru (per kg)</div>
                    <div class="row g-2" id="bulkPreviewRow">
                        <?php foreach ([5, 10, 25] as $weight): ?>
                            <?php $current = (float) ($packageMap[$weight]['current_price'] ?? 0); ?>
                            <div class="col-sm-4">
                                <div class="border rounded-3 p-2 bg-light-subtle">
                                    <div class="small-muted">Beras <?= esc((string) $weight) ?> kg</div>
                                    <div class="fw-semibold">
                                        Saat ini: <span class="text-muted"><?= format_rupiah($current) ?></span>
                                    </div>
                                    <div class="fw-semibold text-primary">
                                        Baru: <span class="bulk-preview" data-current="<?= esc((string) $current) ?>"><?= format_rupiah($current) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-up-down me-1"></i>Terapkan Penyesuaian
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <?php foreach ($products as $product): ?>
        <div class="col-xl-4 col-md-6">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="fw-semibold"><?= esc($product['product_name']) ?></div>
                            <div class="small-muted"><?= esc($product['product_code']) ?> •
                                <?= format_kg($product['weight_kg']) ?></div>
                        </div>
                        <?= status_badge($product['is_active']) ?>
                    </div>

                    <div class="mb-3">
                        <div class="small-muted">Harga Aktif (per kg)</div>
                        <div class="summary-number">
                            <?= $product['current_price'] !== null ? format_rupiah($product['current_price']) : 'Belum diatur' ?>
                        </div>
                    </div>

                    <form action="<?= site_url('/admin/prices/update/' . $product['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Harga Baru (per kg) — koreksi manual</label>
                            <input type="number" min="1" step="0.01" name="price" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">Simpan Harga</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card card-soft">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Riwayat Perubahan Harga</h2>
                <div class="small-muted">Riwayat sederhana untuk mendukung penjelasan perubahan harga.</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Perubahan</th>
                        <th>Status</th>
                        <th>Diubah Oleh</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($priceHistory === []): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat harga.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($priceHistory as $history): ?>
                            <?php
                            $change = (float) ($history['price_change'] ?? 0);
                            if ($change > 0) {
                                $changeClass = 'text-success';
                                $changeText = '+Rp ' . number_format($change, 0, ',', '.');
                            } elseif ($change < 0) {
                                $changeClass = 'text-danger';
                                $changeText = '-Rp ' . number_format(abs($change), 0, ',', '.');
                            } else {
                                $changeClass = 'text-muted';
                                $changeText = '—';
                            }
                            ?>
                            <tr>
                                <td><?= esc($history['product_name'] ?? '-') ?></td>
                                <td><?= format_rupiah($history['price']) ?></td>
                                <td class="fw-semibold <?= $changeClass ?>"><?= esc($changeText) ?></td>
                                <td><?= status_badge($history['is_current'], 'Aktif', 'Arsip') ?></td>
                                <td><?= esc($history['updated_by_name'] ?? '-') ?></td>
                                <td><?= esc(date('d-m-Y H:i', strtotime((string) $history['created_at']))) ?></td>
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
    (function () {
        const deltaInput = document.getElementById('bulkDelta');
        if (!deltaInput) {
            return;
        }

        const previewNodes = document.querySelectorAll('.bulk-preview');
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        function updatePreview() {
            const rawDelta = deltaInput.value.trim();
            const delta = rawDelta === '' ? 0 : Number(rawDelta);
            const safeDelta = Number.isFinite(delta) ? delta : 0;

            previewNodes.forEach((node) => {
                const current = Number(node.dataset.current || 0);
                const next = current + safeDelta;
                node.textContent = formatter.format(next);
                node.classList.toggle('text-danger', next <= 0);
                node.classList.toggle('text-primary', next > 0);
            });
        }

        deltaInput.addEventListener('input', updatePreview);
        updatePreview();
    })();
</script>
<?= $this->endSection() ?>
