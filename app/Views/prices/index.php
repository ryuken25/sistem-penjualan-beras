<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
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
                        <div class="small-muted">Harga Aktif</div>
                        <div class="summary-number">
                            <?= $product['current_price'] !== null ? format_rupiah($product['current_price']) : 'Belum diatur' ?>
                        </div>
                    </div>

                    <form action="<?= site_url('/admin/prices/update/' . $product['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Harga Baru</label>
                            <input type="number" min="1" step="0.01" name="price" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Harga</button>
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
                        <th>Status</th>
                        <th>Diubah Oleh</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($priceHistory === []): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat harga.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($priceHistory as $history): ?>
                            <tr>
                                <td><?= esc($history['product_name'] ?? '-') ?></td>
                                <td><?= format_rupiah($history['price']) ?></td>
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