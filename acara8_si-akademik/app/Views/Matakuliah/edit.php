<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Edit Mata Kuliah</h1><a href="<?= app_url('/matakuliah'); ?>" class="btn btn-secondary">Kembali</a></div>
<div class="card shadow-sm border-0"><div class="card-body">
    <form action="<?= app_url('/matakuliah/update'); ?>" method="POST">
        <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">
        <div class="mb-3"><label for="kode" class="form-label">Kode Mata Kuliah</label><input class="form-control" id="kode" name="kode" maxlength="10" value="<?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
        <div class="mb-3"><label for="nama" class="form-label">Nama Mata Kuliah</label><input class="form-control" id="nama" name="nama" maxlength="150" value="<?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
        <div class="mb-3"><label for="sks" class="form-label">SKS</label><input class="form-control" type="number" id="sks" name="sks" min="1" max="24" value="<?= (int) $item['sks']; ?>" required></div>
        <div class="mb-3"><label for="prodi_id" class="form-label">Program Studi</label><select class="form-select" id="prodi_id" name="prodi_id" required><?php foreach ($prodi as $program): ?><option value="<?= (int) $program['id']; ?>" <?= (int) $program['id'] === (int) $item['prodi_id'] ? 'selected' : ''; ?>><?= htmlspecialchars($program['kode'] . ' - ' . $program['nama'], ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button> <a href="<?= app_url('/matakuliah'); ?>" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
