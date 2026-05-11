<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card card-soft">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h2 class="h5 mb-1">Profil Pengguna</h2>
                    <div class="small-muted">Ubah nama, username, foto profil, dan password akun Anda.</div>
                </div>

                <form action="<?= site_url('/profile/update') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <?php if (!empty($user['profile_photo'])): ?>
                                    <img src="<?= base_url($user['profile_photo']) ?>" alt="Foto Profil"
                                        class="rounded-circle border" style="width: 88px; height: 88px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted"
                                        style="width: 88px; height: 88px;">
                                        <i class="bi bi-person fs-2"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <input type="file" name="profile_photo" class="form-control"
                                        accept=".png,.jpg,.jpeg,.webp">
                                    <div class="small-muted mt-1">Format yang didukung: PNG, JPG, JPEG, dan WEBP.
                                        Maksimal 2 MB.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= esc(old('full_name', $user['full_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control"
                                value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control">
                        <div class="small-muted mt-1">Isi hanya jika ingin mengganti password.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_new_password" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">Perbarui Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>