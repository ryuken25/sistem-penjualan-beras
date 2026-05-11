<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card card-soft">
            <div class="card-body p-4">
                <form action="<?= site_url($formAction) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Produk</label>
                            <input type="text" name="product_code" class="form-control"
                                value="<?= esc(old('product_code', $product['product_code'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="product_name" class="form-control"
                                value="<?= esc(old('product_name', $product['product_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Berat Kemasan</label>
                            <?php $selectedWeight = (string) old('weight_kg', (string) ($product['weight_kg'] ?? '5')); ?>
                            <select name="weight_kg" class="form-select" required>
                                <option value="5" <?= in_array($selectedWeight, ['5', '5.00'], true) ? 'selected' : '' ?>>5 Kg</option>
                                <option value="10" <?= in_array($selectedWeight, ['10', '10.00'], true) ? 'selected' : '' ?>>10 Kg</option>
                                <option value="25" <?= in_array($selectedWeight, ['25', '25.00'], true) ? 'selected' : '' ?>>25 Kg</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Aktif</label>
                            <?php $selectedActive = (string) old('is_active', (string) ($product['is_active'] ?? '1')); ?>
                            <select name="is_active" class="form-select">
                                <option value="1" <?= $selectedActive === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $selectedActive === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= site_url('/admin/products') ?>" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>