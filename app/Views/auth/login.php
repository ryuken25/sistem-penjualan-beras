<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="login-page d-flex align-items-center justify-content-center p-4">
    <div class="card card-soft login-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3"
                    style="width: 72px; height: 72px;">
                    <i class="bi bi-shield-lock fs-2"></i>
                </div>
                <h1 class="h3 fw-bold mb-2">Masuk Internal</h1>
                <p class="text-muted mb-0">Sistem Informasi Penjualan Beras Berbasis Website</p>
                <p class="small-muted mb-0">Studi Kasus UD Tulus Sari Merta</p>
            </div>

            <?= $this->include('partials/flash') ?>

            <form action="<?= site_url('/login') ?>" method="post" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label">Nama Pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?= esc(old('username', '')) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="mt-4 small text-muted bg-light rounded-4 p-3">
                <div class="fw-semibold mb-2">Akun demo bawaan:</div>
                <div>Admin: <strong>admin</strong> / <strong>admin12345</strong></div>
                <div>Pegawai: <strong>pegawai</strong> / <strong>pegawai12345</strong></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
