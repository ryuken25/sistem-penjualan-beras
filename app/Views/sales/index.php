<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-subtitle">
            <?= $isAdmin ? 'Riwayat transaksi penjualan yang tersimpan di database dengan snapshot harga pada saat transaksi.' : 'Riwayat transaksi pegawai hanya menampilkan transaksi yang dicatat pada hari ini.' ?>
        </div>
        <?php if (!empty($showTodayOnlyNotice)): ?>
            <div class="small-muted mt-1">Daftar berikut hanya berisi transaksi hari ini.</div>
        <?php endif; ?>
    </div>
    <?php if ($isAdmin): ?>
        <a href="<?= site_url('/sales/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Input Transaksi Baru
        </a>
    <?php endif; ?>
</div>

<div class="card card-soft">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <?php if ($isAdmin): ?>
                            <th>Nomor Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pencatat</th>
                            <th>Template</th>
                            <th>Detail Item</th>
                            <th>Total KG</th>
                            <th>Total Harga</th>
                        <?php else: ?>
                            <th>Jam</th>
                            <th>Nama Pelanggan</th>
                            <th>Ringkasan Item</th>
                            <th>Total Harga</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions === []): ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? '7' : '4' ?>" class="text-center text-muted py-4">Belum ada
                                transaksi penjualan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php $detailItems = $items[$transaction['id']] ?? []; ?>
                            <tr>
                                <?php if ($isAdmin): ?>
                                    <td class="fw-semibold">
                                        <?= esc($transaction['invoice_number']) ?>
                                    </td>
                                    <td>
                                        <?= esc(date('d-m-Y H:i', strtotime((string) $transaction['transaction_date']))) ?>
                                        <?php if (!empty($transaction['customer_name'])): ?>
                                            <div class="small-muted">Pelanggan:
                                                <?= esc($transaction['customer_name']) ?>
                                            </div>
                                        <?php endif; ?>
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
                                <?php else: ?>
                                    <?php
                                    $ringkasan = [];
                                    if ((int) ($transaction['qty_25kg'] ?? 0) > 0) {
                                        $ringkasan[] = '25kg x ' . (int) $transaction['qty_25kg'];
                                    }
                                    if ((int) ($transaction['qty_10kg'] ?? 0) > 0) {
                                        $ringkasan[] = '10kg x ' . (int) $transaction['qty_10kg'];
                                    }
                                    if ((int) ($transaction['qty_5kg'] ?? 0) > 0) {
                                        $ringkasan[] = '5kg x ' . (int) $transaction['qty_5kg'];
                                    }
                                    ?>
                                    <td class="fw-semibold">
                                        <?= esc(date('H:i', strtotime((string) $transaction['transaction_date']))) ?>
                                    </td>
                                    <td>
                                        <?= esc(trim((string) ($transaction['customer_name'] ?? '')) !== '' ? $transaction['customer_name'] : 'Umum') ?>
                                    </td>
                                    <td>
                                        <?= esc($ringkasan !== [] ? implode(', ', $ringkasan) : '-') ?>
                                    </td>
                                    <td>
                                        <?= format_rupiah($transaction['total_harga'] ?? $transaction['grand_total']) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>