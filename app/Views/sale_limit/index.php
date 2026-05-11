<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-xl-7">
        <div class="card card-soft mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h2 class="h5 mb-2">Status Saat Ini</h2>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?= status_badge($setting['is_enabled'] ?? 0, 'Aktif', 'Nonaktif') ?>
                    </div>
                    <div class="small-muted">
                        <?= esc(sale_limit_text($setting)) ?>
                    </div>
                </div>

                <form action="<?= site_url('/admin/sale-limit/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_enabled" name="is_enabled"
                            <?= !empty($setting['is_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_enabled">Aktifkan mode pembatasan penjualan</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Batas Maksimum Total KG per Transaksi</label>
                        <input type="number" step="0.01" min="0" name="max_total_kg" class="form-control"
                            value="<?= esc(old('max_total_kg', $setting['max_total_kg'] ?? '0')) ?>" required>
                    </div>

                    <div class="small-muted mb-4">Saat aktif, transaksi yang melebihi batas ini akan ditolak saat
                        disimpan.</div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>