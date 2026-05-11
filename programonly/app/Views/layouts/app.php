<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= esc($title ?? 'Sistem Penjualan Beras') ?>
    </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>

<body>
    <div class="d-lg-flex app-shell">
        <?= $this->include('partials/sidebar') ?>

        <div class="content-area d-flex flex-column">
            <nav class="navbar navbar-expand-lg border-bottom topbar px-4 py-3 sticky-top">
                <div class="container-fluid px-0">
                    <div>
                        <h1 class="h4 mb-1 section-title">
                            <?= esc($title ?? 'Dashboard') ?>
                        </h1>
                        <div class="small-muted">Sistem Informasi Penjualan Beras UD Tulus Sari Merta</div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-semibold">
                                <?= esc((string) session('full_name')) ?>
                            </div>
                            <div class="small-muted text-capitalize">
                                <?= esc((string) session('role')) ?>
                            </div>
                        </div>
                        <?php if (session('profile_photo')): ?>
                            <img src="<?= base_url((string) session('profile_photo')) ?>" alt="Foto Profil"
                                class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                style="width: 44px; height: 44px;">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>

            <main class="container-fluid p-4">
                <?= $this->include('partials/flash') ?>
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>