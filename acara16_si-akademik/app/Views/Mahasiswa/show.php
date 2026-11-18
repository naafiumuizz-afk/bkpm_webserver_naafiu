<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Detail Mahasiswa</h1>
    <div>
        <a href="<?= app_url('/mahasiswa/edit') . '?' . http_build_query(['id' => $item->getId()]); ?>" class="btn btn-primary">Edit</a>
        <a href="<?= app_url('/mahasiswa'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">NIM</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt class="col-sm-3">Nama</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($item->getEmail(), ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt class="col-sm-3">Program Studi</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt class="col-sm-3">Angkatan</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($item->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?></dd>
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($item->getStatus(), ENT_QUOTES, 'UTF-8'); ?></dd>
        </dl>
    </div>
</div>
