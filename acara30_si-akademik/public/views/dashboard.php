<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Dashboard</h1>
        <p class="text-muted mb-1">Selamat datang, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>.</p>
        <p class="text-secondary mb-0">Role: <strong><?= htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    </div>
</div>

<div class="row g-3">
    <div class="col-sm-6 col-lg-3">
        <a href="<?= app_url('/mahasiswa'); ?>" class="card h-100 border-0 shadow-sm text-decoration-none">
            <div class="card-body">
                <p class="text-primary fw-bold small mb-2">DATA AKADEMIK</p>
                <h2 class="h5 text-dark mb-2">Mahasiswa</h2>
                <p class="text-secondary small mb-0">Kelola data mahasiswa.</p>
            </div>
        </a>
    </div>
    <?php if ($userRole === 'admin'): ?>
        <div class="col-sm-6 col-lg-3">
            <a href="<?= app_url('/mahasiswa/create'); ?>" class="card h-100 border-0 shadow-sm text-decoration-none" data-admin-only>
                <div class="card-body">
                    <p class="text-success fw-bold small mb-2">AKSI ADMIN</p>
                    <h2 class="h5 text-dark mb-2">Tambah Mahasiswa</h2>
                    <p class="text-secondary small mb-0">Tambahkan data mahasiswa baru.</p>
                </div>
            </a>
        </div>
    <?php endif; ?>
</div>
