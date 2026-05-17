<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$qty5 = old('qty_5kg', $template['qty_5kg'] ?? '0');
$qty10 = old('qty_10kg', $template['qty_10kg'] ?? '0');
$qty25 = old('qty_25kg', $template['qty_25kg'] ?? '0');
$discountPercent = old('discount_percent', $template['discount_percent'] ?? '0');
$productMap = [];
foreach ($products as $product) {
    $productMap[(int) $product['weight_kg']] = $product;
}
$displayCode = $template['template_code'] ?? ($nextCode ?? '—');
?>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card card-soft">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h2 class="h4 mb-0">
                        <?= $template === null ? 'Tambah Template Cepat' : 'Ubah Template Cepat' ?>
                    </h2>
                </div>

                <form action="<?= site_url($formAction) ?>" method="post" id="templateForm">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Kode Template</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= esc((string) $displayCode) ?>" readonly>
                            <div class="form-text">Kode dibuat otomatis oleh sistem.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Nama Template</label>
                            <input type="text" name="template_name" class="form-control"
                                value="<?= esc(old('template_name', $template['template_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Diskon (%)</label>
                            <input type="number" min="0" max="100" step="0.01"
                                name="discount_percent" id="discountInput"
                                class="form-control"
                                value="<?= esc((string) $discountPercent) ?>">
                            <div class="form-text">Kosongkan untuk tanpa diskon.</div>
                        </div>
                        <div class="col-md-2">
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
                                    <h3 class="h5 mb-0">Isi Jumlah Template</h3>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kemasan</th>
                                            <th class="text-center" style="width: 160px;">Jumlah Default (sak)</th>
                                            <th style="width: 220px;">Harga Aktif (per kg)</th>
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

                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-white">
                                        <div class="small-muted">Estimasi Sebelum Diskon</div>
                                        <div class="h4 mb-0 text-secondary" id="grossTotal">Rp 0</div>
                                        <div class="small text-muted">Total kotor (harga × berat × jumlah).</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-primary-subtle">
                                        <div class="small-muted text-primary-emphasis">
                                            Estimasi Setelah Diskon (<span id="discountLabel">0</span>%)
                                        </div>
                                        <div class="h4 mb-0 text-primary" id="netTotal">Rp 0</div>
                                        <div class="small text-muted">Total setelah dikurangi diskon.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="small-muted">Total Jumlah Paket</div>
                                    <div class="summary-number" id="summaryTotalQty">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-primary-subtle h-100">
                                <div class="card-body">
                                    <div class="small-muted text-primary-emphasis">Estimasi Total Harga (setelah diskon)</div>
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

    const discountInput = document.getElementById('discountInput');

    function getDiscountPercent() {
        if (!discountInput) {
            return 0;
        }
        const raw = discountInput.value.trim();
        if (raw === '') {
            return 0;
        }
        const value = Number(raw);
        if (!Number.isFinite(value) || value < 0) {
            return 0;
        }
        return Math.min(value, 100);
    }

    function recalcTemplateSummary() {
        let totalQty = 0;
        let gross = 0;

        document.querySelectorAll('.template-qty').forEach((input) => {
            const weight = Number(input.dataset.weight || 0);
            const price = Number(input.dataset.price || 0);
            const qty = Number(input.value || 0);
            const subtotal = price * qty * weight;

            totalQty += qty;
            gross += subtotal;
            document.getElementById(`subtotal_${weight}`).textContent = formatCurrency(subtotal);
        });

        const discount = getDiscountPercent();
        const net = gross * (1 - (discount / 100));

        document.getElementById('summaryTotalQty').textContent = totalQty;
        document.getElementById('summaryGrandTotal').textContent = formatCurrency(net);
        document.getElementById('grossTotal').textContent = formatCurrency(gross);
        document.getElementById('netTotal').textContent = formatCurrency(net);
        document.getElementById('discountLabel').textContent = discount.toString();
    }

    document.querySelectorAll('.template-qty').forEach((input) => {
        input.addEventListener('input', recalcTemplateSummary);
        input.addEventListener('change', recalcTemplateSummary);
    });

    if (discountInput) {
        discountInput.addEventListener('input', recalcTemplateSummary);
    }

    recalcTemplateSummary();
</script>
<?= $this->endSection() ?>
