<?php $errors = session('errors'); ?>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= esc((string) session('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= esc((string) session('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (is_array($errors) && $errors !== []): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="fw-semibold mb-2">Terdapat data yang perlu diperbaiki:</div>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $error): ?>
                <li>
                    <?= esc((string) $error) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>