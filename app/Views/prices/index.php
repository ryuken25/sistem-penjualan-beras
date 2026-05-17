<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$step = (float) PRICE_STEP_PER_KG;
$basePrice = (float) ($currentBase ?? 0);
$derived = derive_package_prices($basePrice);
?>

<div class="card border-primary mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Set Harga Beras (Patokan = 25 kg per kg)</h2>
                <div class="small-muted">Harga 10 kg dan 5 kg disesuaikan otomatis (+Rp <?= esc(number_format($step, 0, ',', '.')) ?>/kg per step).</div>
            </div>
        </div>

        <form action="<?= site_url('/admin/prices/set-base') ?>" method="post" id="basePriceForm">
            <?= csrf_field() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label" for="basePriceInput">Harga Beras 25 kg (Rp per kg)</label>
                    <input type="number" min="1" step="1" class="form-control" name="base_price" id="basePriceInput"
                        value="<?= esc((string) ($basePrice > 0 ? (int) $basePrice : '')) ?>" required>
                    <div class="form-text">
                        Contoh: 14000. Sistem akan set 10 kg = Rp <?= esc(number_format(14000 + $step, 0, ',', '.')) ?>,
                        5 kg = Rp <?= esc(number_format(14000 + ($step * 2), 0, ',', '.')) ?>.
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="small-muted mb-2">Pratinjau Harga Aktif (per kg)</div>
                    <div class="row g-2">
                        <?php foreach ([25, 10, 5] as $weight): ?>
                            <div class="col-sm-4">
                                <div class="border rounded-3 p-2 bg-light-subtle">
                                    <div class="small-muted">Beras <?= esc((string) $weight) ?> kg</div>
                                    <div class="fw-semibold text-primary">
                                        <span class="base-preview" data-weight="<?= esc((string) $weight) ?>">
                                            <?= format_rupiah($derived[$weight]) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-3 mt-3">
                <span class="small text-muted d-none" id="basePriceHint">Nilai harus berbeda dari harga saat ini.</span>
                <button type="submit" class="btn btn-primary" id="basePriceSubmit" disabled>
                    <i class="bi bi-save me-1"></i>Simpan Harga Pokok
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card card-soft mb-4">
    <div class="card-body">
        <div class="mb-3">
            <h2 class="h5 mb-1">Harga Aktif Kemasan</h2>
            <div class="small-muted">Otomatis menyesuaikan dari harga pokok 25 kg.</div>
        </div>

        <div class="row g-3">
            <?php foreach ([5, 10, 25] as $weight): ?>
                <?php
                $product = $packages[$weight] ?? null;
                $pricePerKg = $product !== null ? (float) ($product['current_price'] ?? 0) : 0.0;
                $totalPerSak = $pricePerKg * $weight;
                ?>
                <div class="col-xl-4 col-md-6">
                    <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-semibold"><?= esc($product['product_name'] ?? ('Beras ' . $weight . ' Kg')) ?></div>
                                <div class="small-muted">
                                    <?= esc($product['product_code'] ?? '-') ?> • <?= format_kg($weight) ?>
                                </div>
                            </div>
                            <?php if ($product !== null): ?>
                                <?= status_badge($product['is_active']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="small-muted mt-2">Total per sak</div>
                        <div class="summary-number">
                            <?= $pricePerKg > 0 ? format_rupiah($totalPerSak) : 'Belum diatur' ?>
                        </div>
                        <?php if ($pricePerKg > 0): ?>
                            <div class="small text-muted">
                                Rincian: <?= format_rupiah($pricePerKg) ?>/kg × <?= esc((string) $weight) ?> kg
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="card-body">
        <div class="mb-3">
            <h2 class="h5 mb-1">Riwayat Perubahan Harga Pokok</h2>
            <div class="small-muted">Hanya menampilkan riwayat harga pokok (Beras 25 kg per kg).</div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-end" style="width: 70px;">Nomor</th>
                        <th class="text-end">Harga Pokok</th>
                        <th class="text-end">Perubahan</th>
                        <th class="text-center">Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($history ?? []) === []): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat harga pokok.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $row): ?>
                            <?php
                            $change = (float) ($row['price_change'] ?? 0);
                            if ($change > 0) {
                                $changeClass = 'text-success fw-bold';
                                $changeText = '+Rp ' . number_format($change, 0, ',', '.');
                            } elseif ($change < 0) {
                                $changeClass = 'text-danger fw-bold';
                                $changeText = '-Rp ' . number_format(abs($change), 0, ',', '.');
                            } else {
                                $changeClass = 'text-muted';
                                $changeText = '—';
                            }
                            ?>
                            <tr>
                                <td class="text-end"><?= esc((string) $row['row_number']) ?></td>
                                <td class="text-end"><?= format_rupiah($row['price']) ?></td>
                                <td class="text-end <?= $changeClass ?>"><?= esc($changeText) ?></td>
                                <td class="text-center"><?= status_badge($row['is_current'], 'Aktif', 'Arsip') ?></td>
                                <td><?= esc(date('d-m-Y H:i', strtotime((string) $row['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        const input = document.getElementById('basePriceInput');
        if (!input) {
            return;
        }

        const step = <?= (float) PRICE_STEP_PER_KG ?>;
        const currentBase = <?= json_encode((float) $basePrice) ?>;
        const previews = document.querySelectorAll('.base-preview');
        const submitBtn = document.getElementById('basePriceSubmit');
        const hint = document.getElementById('basePriceHint');
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        function update() {
            const raw = input.value.trim();
            const base = raw === '' ? 0 : Number(raw);
            const safe = Number.isFinite(base) ? base : 0;

            previews.forEach((node) => {
                const weight = Number(node.dataset.weight || 0);
                let value;
                if (weight === 25) {
                    value = safe;
                } else if (weight === 10) {
                    value = safe + step;
                } else if (weight === 5) {
                    value = safe + (step * 2);
                } else {
                    value = safe;
                }
                node.textContent = formatter.format(value);
            });

            const isValid = Number.isFinite(base) && base > 0 && base !== currentBase;
            submitBtn.disabled = !isValid;
            if (hint) {
                hint.classList.toggle('d-none', isValid);
            }
        }

        input.addEventListener('input', update);
        update();
    })();
</script>
<?= $this->endSection() ?>
