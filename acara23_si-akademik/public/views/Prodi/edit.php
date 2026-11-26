<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">Edit Program Studi</h1><a href="<?= app_url('/prodi'); ?>" class="btn btn-secondary">Kembali</a></div>
<div class="card shadow-sm border-0"><div class="card-body">
    <form action="<?= app_url('/prodi/update'); ?>" method="POST">
        <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">
        <div class="mb-3"><label for="kode" class="form-label">Kode Prodi</label><input class="form-control" id="kode" name="kode" maxlength="10" value="<?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
        <div class="mb-3"><label for="nama" class="form-label">Nama Prodi</label><input class="form-control" id="nama" name="nama" maxlength="100" value="<?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button> <a href="<?= app_url('/prodi'); ?>" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
