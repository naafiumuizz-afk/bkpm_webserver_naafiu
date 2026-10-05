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
            <input type="hidden" name="id" value="1">

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" value="2023001">
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="Andi Pratama">
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <input type="text" class="form-control" id="prodi" name="prodi" value="Teknik Informatika">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
