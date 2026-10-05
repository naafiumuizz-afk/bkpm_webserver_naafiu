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
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="nama@email.com" required>
            </div>

            <div class="mb-3">
                <label for="prodi_id" class="form-label">Program Studi</label>
                <select class="form-select" id="prodi_id" name="prodi_id" required>
                    <option value="">Pilih program studi</option>
                    <?php foreach ($prodi as $item): ?>
                        <option value="<?= (int) $item['id']; ?>">
                            <?= htmlspecialchars($item['kode'] . ' - ' . $item['nama'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="angkatan" class="form-label">Angkatan</label>
                <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="2100" placeholder="2024" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= $baseUrl; ?>/mahasiswa" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
