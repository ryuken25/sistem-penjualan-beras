<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="page-subtitle">Produk difokuskan pada kemasan 5 kg, 10 kg, dan 25 kg sesuai proposal.</div>
    <a href="<?= site_url('/admin/products/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Produk
    </a>
</div>

<div class="card card-soft">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Berat</th>
                        <th>Harga Aktif</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products === []): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data produk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?= esc($product['product_code']) ?>
                                </td>
                                <td>
                                    <?= esc($product['product_name']) ?>
                                </td>
                                <td>
                                    <?= format_kg($product['weight_kg']) ?>
                                </td>
                                <td>
                                    <?= $product['current_price'] !== null ? format_rupiah($product['current_price']) : '<span class="text-muted">Belum diatur</span>' ?>
                                </td>
                                <td>
                                    <?= status_badge($product['is_active']) ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= site_url('/admin/products/edit/' . $product['id']) ?>"
                                            class="btn btn-outline-primary btn-sm">Ubah</a>
                                        <form action="<?= site_url('/admin/products/delete/' . $product['id']) ?>" method="post"
                                            onsubmit="return confirm('Hapus produk ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
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