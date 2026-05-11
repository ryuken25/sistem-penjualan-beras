<?php $isAdmin = is_admin(); ?>

<aside class="sidebar text-white p-4 d-flex flex-column">
    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary-subtle">
        <div class="bg-white text-primary rounded-4 d-flex align-items-center justify-content-center"
            style="width: 52px; height: 52px;">
            <i class="bi bi-box-seam fs-4"></i>
        </div>
        <div>
            <div class="fw-bold">UD Tulus Sari Merta</div>
            <div class="small text-white-50">Sistem Penjualan Beras</div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
        <div class="small text-uppercase text-white-50 mb-2">Menu Utama</div>
        <nav class="nav flex-column mb-4">
            <a class="nav-link <?= active_menu('dashboard', true) ?>" href="<?= site_url('/dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a class="nav-link <?= active_menu('sales') ?>" href="<?= site_url('/sales') ?>">
                <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
            </a>
            <a class="nav-link <?= active_menu('profile') ?>" href="<?= site_url('/profile') ?>">
                <i class="bi bi-person-gear me-2"></i> Profil
            </a>
        </nav>

        <div class="small text-uppercase text-white-50 mb-2">Administrasi</div>
        <nav class="nav flex-column mb-4">
            <a class="nav-link <?= active_menu('admin/users') ?>" href="<?= site_url('/admin/users') ?>">
                <i class="bi bi-people me-2"></i> Kelola Pengguna
            </a>
            <a class="nav-link <?= active_menu('admin/prices') ?>" href="<?= site_url('/admin/prices') ?>">
                <i class="bi bi-cash-stack me-2"></i> Kelola Harga
            </a>
            <a class="nav-link <?= active_menu('admin/templates') ?>" href="<?= site_url('/admin/templates') ?>">
                <i class="bi bi-lightning-charge me-2"></i> Template Cepat
            </a>
            <a class="nav-link <?= active_menu('admin/sale-limit') ?>" href="<?= site_url('/admin/sale-limit') ?>">
                <i class="bi bi-sliders me-2"></i> Mode Limit
            </a>
            <a class="nav-link <?= active_menu('admin/reports') ?>" href="<?= site_url('/admin/reports') ?>">
                <i class="bi bi-graph-up me-2"></i> Laporan Penjualan
            </a>
            <a class="nav-link <?= active_menu('admin/charts') ?>" href="<?= site_url('/admin/charts') ?>">
                <i class="bi bi-bar-chart-line me-2"></i> Grafik Penjualan
            </a>
        </nav>
    <?php else: ?>
        <div class="small text-uppercase text-white-50 mb-2">Menu Pegawai</div>
        <nav class="nav flex-column mb-4">
            <a class="nav-link <?= active_menu('sales/create', true) ?>" href="<?= site_url('/sales/create') ?>">
                <i class="bi bi-receipt me-2"></i> Penjualan
            </a>
            <a class="nav-link <?= active_menu('sales/template', true) ?>" href="<?= site_url('/sales/template') ?>">
                <i class="bi bi-lightning-charge me-2"></i> Template Cepat
            </a>
            <a class="nav-link <?= active_menu('sales', true) ?>" href="<?= site_url('/sales') ?>">
                <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
            </a>
            <a class="nav-link <?= active_menu('profile') ?>" href="<?= site_url('/profile') ?>">
                <i class="bi bi-person-gear me-2"></i> Profil
            </a>
        </nav>
    <?php endif; ?>

    <div class="mt-auto pt-2 border-top border-secondary-subtle">
        <form action="<?= site_url('/logout') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light w-100 rounded-3">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
</aside>