<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$productMap = [];
foreach ($products as $product) {
    $productMap[(int) $product['weight_kg']] = $product;
}

$qty5 = old('qty_5kg', '0');
$qty10 = old('qty_10kg', '0');
$qty25 = old('qty_25kg', '0');
$selectedTemplateId = old('template_id', '');
$transactionDate = old('transaction_date', date('Y-m-d\TH:i'));
$isTemplateMode = ($transactionMode ?? 'manual') === 'template';
?>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card card-soft">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                    <div>
                        <h2 class="h4 mb-1">
                            <?= $isTemplateMode ? 'Transaksi Berdasarkan Template Cepat' : 'Transaksi Penjualan Manual' ?>
                        </h2>
                        <div class="page-subtitle">
                            <?= $isTemplateMode
                                ? 'Pilih template aktif, cek ringkasan pembelian, lalu konfirmasi transaksi.'
                                : 'Masukkan jumlah untuk kemasan 5 kg, 10 kg, dan 25 kg. Sistem menghitung total secara otomatis.' ?>
                        </div>
                    </div>
                    <?php if (!$isTemplateMode): ?>
                        <a href="<?= site_url('/sales/template') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-lightning-charge me-2"></i>Gunakan Template Cepat
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('/sales/create') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Kembali ke Penjualan Manual
                        </a>
                    <?php endif; ?>
                </div>

                <div id="formErrorBox" class="alert alert-danger d-none" role="alert"></div>

                <form action="<?= site_url('/sales/store') ?>" method="post" id="salesForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_transaksi" value="<?= $isTemplateMode ? 'template' : 'manual' ?>">
                    <input type="hidden" name="is_confirmed" id="is_confirmed" value="0">
                    <input type="hidden" name="template_id" id="template_id"
                        value="<?= esc((string) $selectedTemplateId) ?>">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Transaksi</label>
                            <input type="datetime-local" name="transaction_date" id="transaction_date"
                                class="form-control" value="<?= esc((string) $transactionDate) ?>" readonly>
                            <div class="small-muted mt-1">Tanggal dan jam transaksi diisi otomatis oleh sistem.</div>
                        </div>
                        <div class="col-md-6">
                            <?php if ($isTemplateMode): ?>
                                <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="<?= esc(old('customer_name', '')) ?>" placeholder="Nama pelanggan wajib diisi" required>
                                <div class="small-muted mt-1">Wajib diisi. Satu pelanggan hanya bisa 1x per template.</div>
                            <?php else: ?>
                                <label class="form-label">Nama Pelanggan (Opsional)</label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="<?= esc(old('customer_name', '')) ?>" placeholder="Kosongkan jika pembeli umum">
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($isTemplateMode): ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h3 class="h5 mb-1">Daftar Template Cepat</h3>
                                    <div class="small-muted">Pilih salah satu template aktif. Jumlah akan terisi otomatis
                                        sesuai template.</div>
                                </div>
                            </div>

                            <?php if ($templates === []): ?>
                                <div class="alert alert-light border mb-0">Belum ada template aktif yang dapat digunakan.</div>
                            <?php else: ?>
                                <div class="row g-3" id="templateCardGroup">
                                    <?php foreach ($templates as $template): ?>
                                        <?php
                                        $templateQty5 = (int) ($template['qty_5kg'] ?? 0);
                                        $templateQty10 = (int) ($template['qty_10kg'] ?? 0);
                                        $templateQty25 = (int) ($template['qty_25kg'] ?? 0);
                                        $templateDiscount = (float) ($template['discount_percent'] ?? 0);
                                        ?>
                                        <div class="col-md-6">
                                            <button type="button"
                                                class="btn w-100 text-start border rounded-4 p-3 template-select-btn <?= (string) $selectedTemplateId === (string) $template['id'] ? 'btn-primary-subtle border-primary' : 'btn-light' ?>"
                                                data-template-id="<?= esc((string) $template['id']) ?>"
                                                data-template-name="<?= esc($template['template_name']) ?>"
                                                data-qty-5="<?= esc((string) $templateQty5) ?>"
                                                data-qty-10="<?= esc((string) $templateQty10) ?>"
                                                data-qty-25="<?= esc((string) $templateQty25) ?>"
                                                data-discount="<?= esc((string) $templateDiscount) ?>">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="fw-semibold mb-1"><?= esc($template['template_name']) ?></div>
                                                    <?php if ($templateDiscount > 0): ?>
                                                        <span class="badge text-bg-warning">Diskon <?= esc(rtrim(rtrim(number_format($templateDiscount, 2, ',', '.'), '0'), ',')) ?>%</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="small-muted mb-2"><?= esc($template['template_code']) ?></div>
                                                <div class="small text-muted">
                                                    5kg x <?= esc((string) $templateQty5) ?>,
                                                    10kg x <?= esc((string) $templateQty10) ?>,
                                                    25kg x <?= esc((string) $templateQty25) ?>
                                                </div>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card border-0 bg-light-subtle mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <h3 class="h5 mb-1">Input Jumlah Kemasan Beras</h3>
                                    <div class="small-muted">Isi jumlah masing-masing kemasan. Minimal satu jumlah harus
                                        lebih
                                        dari 0.</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kemasan</th>
                                            <th class="text-center" style="width: 140px;">Jumlah (sak)</th>
                                            <th style="width: 180px;">Harga (per kg)</th>
                                            <th style="width: 180px;">Subtotal</th>
                                            <th style="width: 160px;">Total Kg</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ([5, 10, 25] as $weight): ?>
                                            <?php
                                            $product = $productMap[$weight] ?? null;
                                            $price = (float) ($product['current_price'] ?? 0);
                                            $fieldName = 'qty_' . $weight . 'kg';
                                            $fieldValue = ${'qty' . $weight};
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">Beras <?= esc((string) $weight) ?> kg</div>
                                                    <div class="small-muted">
                                                        <?= esc($product['product_code'] ?? 'Produk default') ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" min="0" step="1" inputmode="numeric"
                                                        class="form-control qty-input text-center"
                                                        name="<?= esc($fieldName) ?>" id="<?= esc($fieldName) ?>"
                                                        value="<?= esc((string) $fieldValue) ?>"
                                                        data-weight="<?= esc((string) $weight) ?>"
                                                        data-price="<?= esc((string) $price) ?>">
                                                </td>
                                                <td class="text-nowrap" id="price_<?= esc((string) $weight) ?>">-</td>
                                                <td class="text-nowrap fw-semibold"
                                                    id="subtotal_<?= esc((string) $weight) ?>">Rp 0</td>
                                                <td class="text-nowrap" id="kg_<?= esc((string) $weight) ?>">0 kg</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="small-muted">Total Jumlah</div>
                                    <div class="summary-number" id="summaryTotalItems">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="small-muted">Total Kg</div>
                                    <div class="summary-number" id="summaryTotalKg">0 kg</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-primary-subtle h-100">
                                <div class="card-body">
                                    <div class="small-muted text-primary-emphasis">Total Harga</div>
                                    <div class="summary-number text-primary" id="summaryGrandTotal">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('/sales') ?>" class="btn btn-outline-secondary">Kembali</a>
                        <button type="button" class="btn btn-primary btn-lg" id="finishTransactionBtn">
                            <i class="bi bi-check2-circle me-2"></i>Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-soft mb-4">
            <div class="card-body">
                <h2 class="h5 mb-2">Status Limit Pembelian</h2>
                <div class="mb-2">
                    <?= status_badge($saleLimit['is_enabled'] ?? 0, 'Aktif', 'Nonaktif') ?>
                </div>
                <div class="small-muted mb-0">
                    <?= esc(sale_limit_text($saleLimit)) ?>
                </div>
            </div>
        </div>

        <div class="card card-soft mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Harga Aktif (per kg)</h2>
                <div class="d-grid gap-3">
                    <?php foreach ([5, 10, 25] as $weight): ?>
                        <?php
                        $product = $productMap[$weight] ?? null;
                        $pricePerKg = (float) ($product['current_price'] ?? 0);
                        $pricePerSak = $pricePerKg * $weight;
                        ?>
                        <div class="border rounded-4 p-3 bg-light-subtle">
                            <div class="fw-semibold">Beras <?= esc((string) $weight) ?> kg</div>
                            <div class="small-muted">Harga aktif per kg</div>
                            <div class="h5 mt-2 mb-0"><?= format_rupiah($pricePerKg) ?></div>
                            <div class="small text-muted mt-1">Per sak ≈ <?= format_rupiah($pricePerSak) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (!$isTemplateMode): ?>
            <div class="card card-soft">
                <div class="card-body">
                    <h2 class="h5 mb-2">Template Cepat</h2>
                    <div class="small-muted mb-3">Gunakan menu Template Cepat jika ingin mengisi jumlah otomatis dari paket
                        yang dibuat admin.</div>
                    <a href="<?= site_url('/sales/template') ?>" class="btn btn-outline-primary w-100">
                        <i class="bi bi-lightning-charge me-2"></i>Buka Template Cepat
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="confirmTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h2 class="modal-title h4 mb-1">Konfirmasi Transaksi</h2>
                    <div class="small-muted">Apakah pesanan sudah benar?</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="small-muted">Tanggal Transaksi</div>
                        <div class="fw-semibold" id="confirmTransactionDate">-</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-muted">Nama Pelanggan</div>
                        <div class="fw-semibold" id="confirmCustomerName">Umum</div>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kemasan</th>
                                <th>Jumlah (sak)</th>
                                <th>Harga (per kg)</th>
                                <th>Subtotal</th>
                                <th>Total Kg</th>
                            </tr>
                        </thead>
                        <tbody id="confirmItemsBody"></tbody>
                    </table>
                </div>

                <div id="confirmDiscountBox" class="d-none mb-3 p-3 rounded-3 bg-warning-subtle">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal Kotor</span>
                        <strong id="confirmGrossTotal">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between text-danger">
                        <span>Diskon Template (<span id="confirmDiscountPercent">0</span>%)</span>
                        <strong id="confirmDiscountAmount">-Rp 0</strong>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="small-muted">Total Jumlah</div>
                                <div class="h4 mb-0" id="confirmTotalItems">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="small-muted">Total Kg</div>
                                <div class="h4 mb-0" id="confirmTotalKg">0 kg</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-primary-subtle h-100">
                            <div class="card-body">
                                <div class="small-muted text-primary-emphasis">Total Harga</div>
                                <div class="h4 mb-0 text-primary" id="confirmGrandTotal">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSaveBtn">Simpan / Konfirmasi</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const packagePrices = <?= json_encode([
        5 => (float) (($productMap[5]['current_price'] ?? 0)),
        10 => (float) (($productMap[10]['current_price'] ?? 0)),
        25 => (float) (($productMap[25]['current_price'] ?? 0)),
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    const templates = <?= json_encode(array_map(static function ($template) {
        return [
            'id' => (int) $template['id'],
            'name' => $template['template_name'],
            'qty_5kg' => (int) ($template['qty_5kg'] ?? 0),
            'qty_10kg' => (int) ($template['qty_10kg'] ?? 0),
            'qty_25kg' => (int) ($template['qty_25kg'] ?? 0),
            'discount_percent' => (float) ($template['discount_percent'] ?? 0),
        ];
    }, $templates), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    let activeDiscountPercent = 0;

    const qtyInputs = {
        5: document.getElementById('qty_5kg'),
        10: document.getElementById('qty_10kg'),
        25: document.getElementById('qty_25kg'),
    };
    const templateIdInput = document.getElementById('template_id');
    const confirmedInput = document.getElementById('is_confirmed');
    const transactionDateInput = document.getElementById('transaction_date');
    const customerNameInput = document.querySelector('input[name="customer_name"]');
    const finishButton = document.getElementById('finishTransactionBtn');
    const salesForm = document.getElementById('salesForm');
    const formErrorBox = document.getElementById('formErrorBox');
    const confirmModalElement = document.getElementById('confirmTransactionModal');
    const confirmModal = new bootstrap.Modal(confirmModalElement);
    const isTemplateMode = <?= $isTemplateMode ? 'true' : 'false' ?>;

    const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value || 0));

    const formatKg = (value) => new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value || 0)) + ' kg';

    function normalizeQty(value) {
        const raw = String(value ?? '').trim();
        if (raw === '') {
            return 0;
        }

        if (!/^\d+$/.test(raw)) {
            return NaN;
        }

        return Number(raw);
    }

    function getState() {
        const qty5 = normalizeQty(qtyInputs[5].value);
        const qty10 = normalizeQty(qtyInputs[10].value);
        const qty25 = normalizeQty(qtyInputs[25].value);

        const invalidField = [qty5, qty10, qty25].some((qty) => Number.isNaN(qty));
        const subtotal5 = (Number.isNaN(qty5) ? 0 : qty5) * 5 * packagePrices[5];
        const subtotal10 = (Number.isNaN(qty10) ? 0 : qty10) * 10 * packagePrices[10];
        const subtotal25 = (Number.isNaN(qty25) ? 0 : qty25) * 25 * packagePrices[25];

        const gross = subtotal5 + subtotal10 + subtotal25;
        const discountPercent = isTemplateMode ? activeDiscountPercent : 0;
        const discountAmount = Math.round(gross * (discountPercent / 100));
        const grandTotal = gross - discountAmount;

        return {
            invalidField,
            qty: { 5: Number.isNaN(qty5) ? 0 : qty5, 10: Number.isNaN(qty10) ? 0 : qty10, 25: Number.isNaN(qty25) ? 0 : qty25 },
            subtotal: { 5: subtotal5, 10: subtotal10, 25: subtotal25 },
            totalItems: (Number.isNaN(qty5) ? 0 : qty5) + (Number.isNaN(qty10) ? 0 : qty10) + (Number.isNaN(qty25) ? 0 : qty25),
            totalKg: ((Number.isNaN(qty5) ? 0 : qty5) * 5) + ((Number.isNaN(qty10) ? 0 : qty10) * 10) + ((Number.isNaN(qty25) ? 0 : qty25) * 25),
            gross,
            discountPercent,
            discountAmount,
            grandTotal,
        };
    }

    function renderSummary() {
        const state = getState();

        [5, 10, 25].forEach((weight) => {
            document.getElementById(`price_${weight}`).textContent = formatCurrency(packagePrices[weight]);
            document.getElementById(`subtotal_${weight}`).textContent = formatCurrency(state.subtotal[weight]);
            document.getElementById(`kg_${weight}`).textContent = formatKg(state.qty[weight] * weight);
        });

        document.getElementById('summaryTotalItems').textContent = state.totalItems;
        document.getElementById('summaryTotalKg').textContent = formatKg(state.totalKg);
        document.getElementById('summaryGrandTotal').textContent = formatCurrency(state.grandTotal);
    }

    function showError(message) {
        formErrorBox.textContent = message;
        formErrorBox.classList.remove('d-none');
    }

    function clearError() {
        formErrorBox.textContent = '';
        formErrorBox.classList.add('d-none');
    }

    function validateBeforeConfirm() {
        const state = getState();

        if (state.invalidField) {
            showError('Jumlah hanya boleh berupa angka bulat 0 atau lebih.');
            return null;
        }

        if (state.totalItems <= 0) {
            showError('Minimal satu jumlah harus diisi.');
            return null;
        }

        if (isTemplateMode && !templateIdInput.value) {
            showError('Silakan pilih salah satu template cepat terlebih dahulu.');
            return null;
        }

        if (isTemplateMode && customerNameInput.value.trim() === '') {
            showError('Nama pelanggan wajib diisi saat memakai template cepat.');
            return null;
        }

        const limitEnabled = <?= !empty($saleLimit['is_enabled']) ? 'true' : 'false' ?>;
        const maxTotalKg = <?= (float) ($saleLimit['max_total_kg'] ?? 0) ?>;
        if (limitEnabled && state.totalKg > maxTotalKg) {
            showError('Pembelian melebihi limit maksimum.');
            return null;
        }

        clearError();
        return state;
    }

    function fillConfirmation(state) {
        document.getElementById('confirmTransactionDate').textContent = transactionDateInput.value.replace('T', ' ');
        document.getElementById('confirmCustomerName').textContent = customerNameInput.value.trim() !== '' ? customerNameInput.value.trim() : 'Umum';
        document.getElementById('confirmTotalItems').textContent = state.totalItems;
        document.getElementById('confirmTotalKg').textContent = formatKg(state.totalKg);
        document.getElementById('confirmGrandTotal').textContent = formatCurrency(state.grandTotal);

        const discountBox = document.getElementById('confirmDiscountBox');
        if (state.discountPercent > 0 && state.discountAmount > 0) {
            document.getElementById('confirmGrossTotal').textContent = formatCurrency(state.gross);
            document.getElementById('confirmDiscountPercent').textContent = String(state.discountPercent);
            document.getElementById('confirmDiscountAmount').textContent = '-' + formatCurrency(state.discountAmount);
            discountBox.classList.remove('d-none');
        } else {
            discountBox.classList.add('d-none');
        }

        const tbody = document.getElementById('confirmItemsBody');
        tbody.innerHTML = '';
        [5, 10, 25].forEach((weight) => {
            if (state.qty[weight] <= 0) {
                return;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>Beras ${weight} kg</td>
                <td>${state.qty[weight]}</td>
                <td>${formatCurrency(packagePrices[weight])}</td>
                <td>${formatCurrency(state.subtotal[weight])}</td>
                <td>${formatKg(state.qty[weight] * weight)}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function applyTemplate(templateId) {
        const selected = templates.find((template) => String(template.id) === String(templateId));
        if (!selected) {
            return;
        }

        templateIdInput.value = selected.id;
        qtyInputs[5].value = selected.qty_5kg;
        qtyInputs[10].value = selected.qty_10kg;
        qtyInputs[25].value = selected.qty_25kg;
        activeDiscountPercent = Number(selected.discount_percent || 0);

        if (isTemplateMode) {
            [5, 10, 25].forEach((weight) => {
                qtyInputs[weight].setAttribute('readonly', 'readonly');
                qtyInputs[weight].classList.add('bg-light');
                qtyInputs[weight].setAttribute('tabindex', '-1');
            });
        }

        document.querySelectorAll('.template-select-btn').forEach((button) => {
            const active = button.dataset.templateId === String(templateId);
            button.classList.toggle('btn-primary-subtle', active);
            button.classList.toggle('border-primary', active);
            button.classList.toggle('btn-light', !active);
        });

        renderSummary();
    }

    document.querySelectorAll('.qty-input').forEach((input) => {
        input.addEventListener('input', renderSummary);
        input.addEventListener('change', renderSummary);
    });

    document.querySelectorAll('.template-select-btn').forEach((button) => {
        button.addEventListener('click', () => applyTemplate(button.dataset.templateId));
    });

    finishButton.addEventListener('click', () => {
        const state = validateBeforeConfirm();
        if (!state) {
            return;
        }

        fillConfirmation(state);
        confirmedInput.value = '0';
        confirmModal.show();
    });

    document.getElementById('confirmSaveBtn').addEventListener('click', () => {
        confirmedInput.value = '1';
        confirmModal.hide();
        salesForm.submit();
    });

    confirmModalElement.addEventListener('hidden.bs.modal', () => {
        if (confirmedInput.value !== '1') {
            confirmedInput.value = '0';
        }
    });

    if (isTemplateMode) {
        // Kunci qty inputs sampai template dipilih
        [5, 10, 25].forEach((weight) => {
            qtyInputs[weight].setAttribute('readonly', 'readonly');
            qtyInputs[weight].classList.add('bg-light');
            qtyInputs[weight].setAttribute('tabindex', '-1');
        });
    }

    renderSummary();
    if (isTemplateMode && templateIdInput.value) {
        applyTemplate(templateIdInput.value);
    }
</script>
<?= $this->endSection() ?>