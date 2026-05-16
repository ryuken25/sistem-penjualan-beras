<?php
$source = (string) ($transaction['source_transaksi'] ?? 'manual');
$sourceLabel = $source === 'template' ? 'Template Cepat' : 'Manual';
$templateName = (string) ($transaction['template_name'] ?? '');
if ($source === 'template' && $templateName !== '') {
    $sourceLabel .= ' (' . $templateName . ')';
}

$customerName = trim((string) ($transaction['customer_name'] ?? ''));
$customerDisplay = $customerName !== '' ? $customerName : 'Pelanggan Umum';

$totalSak = 0;
$totalKg = 0.0;
foreach ($items as $item) {
    $totalSak += (int) $item['quantity'];
    $totalKg += (float) $item['total_kg_item'];
}
?><!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice <?= esc($transaction['invoice_number']) ?> - UD Tulus Sari Merta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f5f6f8; color: #212529; }
        .invoice-wrap { max-width: 720px; margin: 24px auto; }
        .invoice-paper { background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); }
        .invoice-title { font-weight: 700; letter-spacing: 2px; }
        .shop-name { font-weight: 700; }
        .meta-label { font-size: 0.78rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-value { font-weight: 600; }
        table.invoice-table th { background: #f8f9fa; }
        .total-line { font-size: 1.4rem; font-weight: 700; }
        .action-bar { max-width: 720px; margin: 16px auto 0; display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }
        @media print {
            body { background: #fff; }
            .no-print, .action-bar { display: none !important; }
            .invoice-wrap { margin: 0; max-width: 100%; }
            .invoice-paper { box-shadow: none; border-radius: 0; padding: 0; }
            @page { size: A5; margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="action-bar no-print">
        <a href="<?= site_url('/sales') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Riwayat
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak / Simpan PDF
        </button>
        <a href="<?= site_url('/sales/create') ?>" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Transaksi Baru
        </a>
    </div>

    <div class="invoice-wrap">
        <div class="invoice-paper">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <div class="invoice-title h3 mb-1">INVOICE</div>
                    <div class="text-muted small">No. <?= esc($transaction['invoice_number']) ?></div>
                </div>
                <div class="text-end">
                    <div class="shop-name h5 mb-1">UD TULUS SARI MERTA</div>
                    <div class="small text-muted">Sistem Informasi Penjualan Beras</div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="meta-label">Tanggal</div>
                    <div class="meta-value"><?= esc(date('d-m-Y H:i', strtotime((string) $transaction['transaction_date']))) ?></div>
                </div>
                <div class="col-6">
                    <div class="meta-label">Pelanggan</div>
                    <div class="meta-value"><?= esc($customerDisplay) ?></div>
                </div>
                <div class="col-6">
                    <div class="meta-label">Kasir</div>
                    <div class="meta-value"><?= esc($transaction['created_by_name'] ?? '-') ?></div>
                </div>
                <div class="col-6">
                    <div class="meta-label">Sumber</div>
                    <div class="meta-value"><?= esc($sourceLabel) ?></div>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table invoice-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kemasan</th>
                            <th class="text-center">Jumlah (sak)</th>
                            <th class="text-center">Berat/sak</th>
                            <th class="text-end">Harga/kg</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($items === []): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Tidak ada item.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= esc($item['product_name_snapshot']) ?></td>
                                    <td class="text-center"><?= esc((string) $item['quantity']) ?></td>
                                    <td class="text-center"><?= format_kg($item['weight_kg_snapshot']) ?></td>
                                    <td class="text-end"><?= format_rupiah($item['unit_price_snapshot']) ?></td>
                                    <td class="text-end"><?= format_rupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 border-top pt-3">
                <div class="small text-muted">
                    <?= esc((string) $totalSak) ?> sak &middot; <?= format_kg($totalKg) ?>
                </div>
                <div class="text-end">
                    <div class="meta-label">Total Pembayaran</div>
                    <div class="total-line text-primary"><?= format_rupiah($transaction['grand_total']) ?></div>
                </div>
            </div>

            <?php if (!empty($transaction['notes'])): ?>
                <div class="mt-3 small">
                    <span class="meta-label">Catatan:</span>
                    <div><?= esc($transaction['notes']) ?></div>
                </div>
            <?php endif; ?>

            <div class="text-center small text-muted mt-4">
                Terima kasih atas pembelian Anda.
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 350));
    </script>
</body>
</html>
