<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Tambah Mahasiswa</h1>
    <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-secondary">Kembali</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= $baseUrl; ?>/mahasiswa" method="POST">
            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM">
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama">
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <input type="text" class="form-control" id="prodi" name="prodi" placeholder="Masukkan prodi">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
