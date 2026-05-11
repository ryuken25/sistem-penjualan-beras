<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$qty5 = old('qty_5kg', $template['qty_5kg'] ?? '0');
$qty10 = old('qty_10kg', $template['qty_10kg'] ?? '0');
$qty25 = old('qty_25kg', $template['qty_25kg'] ?? '0');
$productMap = [];
foreach ($products as $product) {
    $productMap[(int) $product['weight_kg']] = $product;
}
?>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card card-soft">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h2 class="h4 mb-1">
                        <?= $template === null ? 'Tambah Template Cepat' : 'Edit Template Cepat' ?>
                    </h2>
                    <div class="page-subtitle">Template berisi jumlah tetap untuk kemasan 5 kg, 10 kg, dan 25 kg.</div>
                </div>

                <form action="<?= site_url($formAction) ?>" method="post" id="templateForm">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Kode Template</label>
                            <input type="text" name="template_code" class="form-control"
                                value="<?= esc(old('template_code', $template['template_code'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Nama Template</label>
                            <input type="text" name="template_name" class="form-control"
                                value="<?= esc(old('template_name', $template['template_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Aktif</label>
                            <?php $selectedActive = (string) old('is_active', (string) ($template['is_active'] ?? '1')); ?>
                            <select name="is_active" class="form-select">
                                <option value="1" <?= $selectedActive === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $selectedActive === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <h3 class="h5 mb-1">Isi Qty Template</h3>
                                    <div class="small-muted">Admin hanya menentukan qty untuk kemasan tetap yang
                                        tersedia di sistem.</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kemasan</th>
                                            <th class="text-center" style="width: 160px;">Qty Default</th>
                                            <th style="width: 220px;">Harga Aktif</th>
                                            <th style="width: 220px;">Estimasi Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ([5, 10, 25] as $weight): ?>
                                            <?php
                                            $product = $productMap[$weight] ?? null;
                                            $price = (float) ($product['current_price'] ?? 0);
                                            $field = 'qty_' . $weight . 'kg';
                                            $value = ${'qty' . $weight};
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">Beras
                                                        <?= esc((string) $weight) ?> kg
                                                    </div>
                                                    <div class="small-muted">Harga mengikuti pengaturan admin saat transaksi
                                                        dipakai</div>
                                                </td>
                                                <td>
                                                    <input type="number" min="0" step="1" name="<?= esc($field) ?>"
                                                        id="<?= esc($field) ?>"
                                                        class="form-control template-qty text-center"
                                                        value="<?= esc((string) $value) ?>"
                                                        data-weight="<?= esc((string) $weight) ?>"
                                                        data-price="<?= esc((string) $price) ?>">
                                                </td>
                                                <td class="text-nowrap">
                                                    <?= format_rupiah($price) ?>
                                                </td>
                                                <td class="text-nowrap fw-semibold"
                                                    id="subtotal_<?= esc((string) $weight) ?>">Rp 0</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="small-muted">Total Qty Paket</div>
                                    <div class="summary-number" id="summaryTotalQty">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-primary-subtle h-100">
                                <div class="card-body">
                                    <div class="small-muted text-primary-emphasis">Estimasi Total Harga</div>
                                    <div class="summary-number text-primary" id="summaryGrandTotal">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('/admin/templates') ?>" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value || 0));

    function recalcTemplateSummary() {
        let totalQty = 0;
        let grandTotal = 0;

        document.querySelectorAll('.template-qty').forEach((input) => {
            const weight = Number(input.dataset.weight || 0);
            const price = Number(input.dataset.price || 0);
            const qty = Number(input.value || 0);
            const subtotal = price * qty;

            totalQty += qty;
            grandTotal += subtotal;
            document.getElementById(`subtotal_${weight}`).textContent = formatCurrency(subtotal);
        });

        document.getElementById('summaryTotalQty').textContent = totalQty;
        document.getElementById('summaryGrandTotal').textContent = formatCurrency(grandTotal);
    }

    document.querySelectorAll('.template-qty').forEach((input) => {
        input.addEventListener('input', recalcTemplateSummary);
        input.addEventListener('change', recalcTemplateSummary);
    });

    recalcTemplateSummary();
</script>
<?= $this->endSection() ?>