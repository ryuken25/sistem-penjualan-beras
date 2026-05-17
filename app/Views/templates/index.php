<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="page-subtitle">Template transaksi cepat berisi kombinasi produk dan jumlah default untuk mempercepat
        input transaksi.</div>
    <a href="<?= site_url('/admin/templates/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Template
    </a>
</div>

<div class="row g-4">
    <?php if ($templates === []): ?>
        <div class="col-12">
            <div class="card card-soft">
                <div class="card-body text-center text-muted py-5">Belum ada template transaksi.</div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($templates as $template): ?>
            <?php
            $templateRows = $templateItems[$template['id']] ?? [];
            $estimatedGross = 0.0;
            $discountPercent = (float) ($template['discount_percent'] ?? 0);
            ?>
            <div class="col-xl-6">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3 gap-3">
                            <div>
                                <div class="fw-semibold fs-5">
                                    <?= esc($template['template_name']) ?>
                                </div>
                                <div class="small-muted">
                                    <?= esc($template['template_code']) ?> • Dibuat oleh
                                    <?= esc($template['created_by_name'] ?? '-') ?>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <?= status_badge($template['is_active']) ?>
                                <?php if ($discountPercent > 0): ?>
                                    <span class="badge text-bg-warning">Diskon <?= esc(rtrim(rtrim(number_format($discountPercent, 2, ',', '.'), '0'), ',')) ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="small-muted mb-2">Daftar Item Template</div>
                            <ul class="transaction-item-list ps-3 mb-0">
                                <?php if ($templateRows === []): ?>
                                    <li class="text-muted">Belum ada item.</li>
                                <?php else: ?>
                                    <?php foreach ($templateRows as $row): ?>
                                        <?php
                                        $itemWeight = (float) ($row['weight_kg'] ?? 0);
                                        $itemPrice = (float) ($row['current_price'] ?? 0);
                                        $itemQty = (int) $row['quantity'];
                                        $estimatedGross += $itemWeight * $itemQty * $itemPrice;
                                        ?>
                                        <li>
                                            <span class="fw-semibold">
                                                <?= esc($row['product_name'] ?? '-') ?>
                                            </span>
                                            <span class="small-muted">• Jumlah
                                                <?= esc((string) $row['quantity']) ?> sak •
                                                <?= format_kg($row['weight_kg'] ?? 0) ?>/sak
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <?php $estimatedNet = $estimatedGross * (1 - ($discountPercent / 100)); ?>
                        <div class="small-muted mb-1">
                            Estimasi total (harga aktif × diskon template):
                            <strong class="text-primary"><?= format_rupiah($estimatedNet) ?></strong>
                        </div>
                        <?php if ($discountPercent > 0): ?>
                            <div class="small text-muted mb-3">
                                Sebelum diskon: <?= format_rupiah($estimatedGross) ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-3"></div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= site_url('/admin/templates/edit/' . $template['id']) ?>"
                                class="btn btn-outline-primary btn-sm">Ubah</a>
                            <form action="<?= site_url('/admin/templates/delete/' . $template['id']) ?>" method="post"
                                onsubmit="return confirm('Hapus template ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>