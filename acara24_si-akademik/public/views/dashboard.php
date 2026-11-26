<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Selamat datang di Sistem Informasi Akademik.</p>
    </div>
    <a href="<?= app_url('/logout'); ?>" class="btn btn-outline-danger">Logout</a>
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
    <div class="col-sm-6 col-lg-3">
        <a href="<?= app_url('/prodi'); ?>" class="card h-100 border-0 shadow-sm text-decoration-none">
            <div class="card-body">
                <p class="text-success fw-bold small mb-2">DATA AKADEMIK</p>
                <h2 class="h5 text-dark mb-2">Program Studi</h2>
                <p class="text-secondary small mb-0">Kelola program studi.</p>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="<?= app_url('/matakuliah'); ?>" class="card h-100 border-0 shadow-sm text-decoration-none">
            <div class="card-body">
                <p class="text-info fw-bold small mb-2">DATA AKADEMIK</p>
                <h2 class="h5 text-dark mb-2">Mata Kuliah</h2>
                <p class="text-secondary small mb-0">Kelola mata kuliah.</p>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="<?= app_url('/dosen'); ?>" class="card h-100 border-0 shadow-sm text-decoration-none">
            <div class="card-body">
                <p class="text-warning fw-bold small mb-2">DATA AKADEMIK</p>
                <h2 class="h5 text-dark mb-2">Dosen</h2>
                <p class="text-secondary small mb-0">Kelola data dosen.</p>
            </div>
        </a>
    </div>
</div>
