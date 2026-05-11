<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card card-soft">
            <div class="card-body p-4">
                <form action="<?= site_url($formAction) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= esc(old('full_name', $user['full_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="username" class="form-control"
                                value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Peran</label>
                            <select name="role" class="form-select" required>
                                <?php $selectedRole = old('role', $user['role'] ?? 'pegawai'); ?>
                                <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="pegawai" <?= $selectedRole === 'pegawai' ? 'selected' : '' ?>>Pegawai
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Aktif</label>
                            <?php $selectedActive = (string) old('is_active', (string) ($user['is_active'] ?? '1')); ?>
                            <select name="is_active" class="form-select">
                                <option value="1" <?= $selectedActive === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $selectedActive === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kata Sandi
                                <?= isset($user) && $user !== null ? '(Kosongkan jika tidak diubah)' : '' ?>
                            </label>
                            <input type="password" name="password" class="form-control" <?= empty($user) ? 'required' : '' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" class="form-control" <?= empty($user) ? 'required' : '' ?>>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= site_url('/admin/users') ?>" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
