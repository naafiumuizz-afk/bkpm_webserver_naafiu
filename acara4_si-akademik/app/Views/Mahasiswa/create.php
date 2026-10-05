<?php
$pageTitle = 'Tambah Mahasiswa';

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Tambah Mahasiswa</h1>
    <a href="<?= htmlspecialchars($baseUrl . '?page=index', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Kembali</a>
</div>

<?php if ($flashMessage !== null): ?>
    <div class="alert alert-warning" role="alert"><?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= htmlspecialchars($baseUrl . '?page=store', ENT_QUOTES, 'UTF-8'); ?>" method="POST">
            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM" pattern="[0-9]{2,}" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" minlength="3" required>
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <input type="text" class="form-control" id="prodi" name="prodi" placeholder="Masukkan prodi" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= htmlspecialchars($baseUrl . '?page=index', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
