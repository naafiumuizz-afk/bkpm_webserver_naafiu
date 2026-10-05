<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
$pageTitle = 'Edit Mahasiswa';

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Edit Mahasiswa</h1>
    <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-secondary">Kembali</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= $baseUrl; ?>/mahasiswa/update" method="POST">
            <input type="hidden" name="id" value="<?= $item->getId(); ?>">

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($item->getEmail(), ENT_QUOTES, 'UTF-8'); ?>" required>

            <div class="mb-3">
                <label for="prodi_id" class="form-label">ID Prodi</label>
                <input type="number" class="form-control" id="prodi_id" name="prodi_id" min="1" value="<?= $item->getProdiId(); ?>" required>
            </div>

            <div class="mb-3">
                <label for="angkatan" class="form-label">Angkatan</label>
                <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="2100" value="<?= htmlspecialchars($item->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
