<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-subtitle">Kelola data admin dan pegawai yang dapat mengakses sistem.</div>
    </div>
    <a href="<?= site_url('/admin/users/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
    </a>
</div>

<div class="card card-soft">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users === []): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        <?= esc($user['full_name']) ?>
                                    </div>
                                    <?php if ((int) current_user_id() === (int) $user['id']): ?>
                                        <div class="small-muted">Akun yang sedang digunakan</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= esc($user['username']) ?>
                                </td>
                                <td>
                                    <?= role_badge($user['role']) ?>
                                </td>
                                <td>
                                    <?= status_badge($user['is_active']) ?>
                                </td>
                                <td>
                                    <?= esc(date('d-m-Y H:i', strtotime((string) $user['created_at']))) ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= site_url('/admin/users/edit/' . $user['id']) ?>"
                                            class="btn btn-outline-primary btn-sm">Edit</a>
                                        <form action="<?= site_url('/admin/users/delete/' . $user['id']) ?>" method="post"
                                            onsubmit="return confirm('Hapus pengguna ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>