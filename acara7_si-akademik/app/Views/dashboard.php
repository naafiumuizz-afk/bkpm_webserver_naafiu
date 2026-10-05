<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Selamat datang di Sistem Informasi Akademik.</p>
    </div>
    <a href="<?= app_url('/mahasiswa'); ?>" class="btn btn-primary">Kelola Mahasiswa</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h2 class="h5">Akses terlindungi</h2>
        <p class="mb-0">Halaman ini hanya dapat diakses setelah login melalui AuthMiddleware.</p>
    </div>
</div>
