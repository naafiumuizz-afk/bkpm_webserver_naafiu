<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Edit Mahasiswa</h1>
    <a href="<?= app_url('/mahasiswa'); ?>" class="btn btn-secondary">Kembali</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= app_url('/mahasiswa/update'); ?>" method="POST">
            <input type="hidden" name="id" value="<?= (int) $item->getId(); ?>">

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" maxlength="20" value="<?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" maxlength="100" value="<?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="100" value="<?= htmlspecialchars($item->getEmail(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="prodi_id" class="form-label">Program Studi</label>
                <select class="form-select" id="prodi_id" name="prodi_id" required>
                    <?php foreach ($prodi as $program): ?>
                        <option value="<?= (int) $program['id']; ?>" <?= (int) $program['id'] === $item->getProdiId() ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($program['kode'] . ' - ' . $program['nama'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="angkatan" class="form-label">Angkatan</label>
                <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="2100" value="<?= htmlspecialchars($item->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= app_url('/mahasiswa'); ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
