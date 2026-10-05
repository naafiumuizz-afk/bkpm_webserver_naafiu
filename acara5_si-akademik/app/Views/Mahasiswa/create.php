<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Tambah Mahasiswa</h1>
    <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-secondary">Kembali</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if ($error !== null): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form action="<?= $baseUrl; ?>/mahasiswa" method="POST">
            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($values['nim'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Masukkan NIM" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($values['nama'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Masukkan nama" required>
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <input type="text" class="form-control" id="prodi" name="prodi" value="<?= htmlspecialchars($values['prodi'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Masukkan prodi" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
