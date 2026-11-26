<?php include __DIR__ . '/../partials/data-list-styles.php'; ?>

<section class="page-heading rounded-4 p-3 p-lg-4 mb-4 d-flex flex-column justify-content-center">
    <p class="text-uppercase text-success small fw-bold mb-2">Portal Akademik</p>
    <h1 class="h2 fw-bold mb-2">Tambah Program Studi</h1>
    <p class="text-secondary mb-0">Masukkan informasi program studi baru.</p>
</section>

<section class="card form-card border-0 rounded-4 shadow-sm">
    <div class="card-body p-3 p-md-4">
        <form action="<?= app_url('/prodi'); ?>" method="POST">
            <div class="mb-3">
                <label for="kode" class="form-label">Kode Prodi</label>
                <input class="form-control" id="kode" name="kode" maxlength="10" required>
            </div>
            <div class="mb-4">
                <label for="nama" class="form-label">Nama Prodi</label>
                <input class="form-control" id="nama" name="nama" maxlength="100" required>
            </div>
            <button class="btn btn-success" type="submit" data-admin-only>Simpan</button>
            <a href="<?= app_url('/prodi'); ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</section>
