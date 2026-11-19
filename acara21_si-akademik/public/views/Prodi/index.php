<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Program Studi</h1>
    <a href="<?= app_url('/prodi/create'); ?>" class="btn btn-primary">Tambah Prodi</a>
</div>
<form class="row g-2 mb-3" method="GET" action="<?= app_url('/prodi'); ?>">
    <div class="col-sm-8 col-md-5"><label for="search" class="visually-hidden">Cari kode atau nama Prodi</label><input class="form-control" type="search" id="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cari kode atau nama Prodi"></div>
    <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Cari</button></div>
    <?php if ($search !== ''): ?><div class="col-auto"><a class="btn btn-outline-secondary" href="<?= app_url('/prodi'); ?>">Reset</a></div><?php endif; ?>
</form>
<div class="table-responsive"><table class="table table-striped table-bordered align-middle">
    <thead class="table-dark"><tr><th>Kode</th><th>Nama Program Studi</th><th class="text-center">Aksi</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr><td><?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8'); ?></td><td><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8'); ?></td><td class="text-center">
                <a class="btn btn-sm btn-outline-primary" href="<?= app_url('/prodi/' . (int) $item['id'] . '/edit'); ?>">Edit</a>
                <form class="d-inline" action="<?= app_url('/prodi/destroy'); ?>" method="POST" onsubmit="return confirm('Hapus program studi ini?')"><input type="hidden" name="id" value="<?= (int) $item['id']; ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button></form>
            </td></tr>
        <?php endforeach; ?>
        <?php if ($items === []): ?><tr><td colspan="3" class="text-center py-4">Data program studi tidak ditemukan.</td></tr><?php endif; ?>
    </tbody>
</table></div>
<div class="d-flex justify-content-between align-items-center">
    <small class="text-muted">Menampilkan <?= count($items); ?> dari <?= $total; ?> data</small>
    <?php if ($pages > 1): ?><nav aria-label="Navigasi halaman program studi"><ul class="pagination pagination-sm mb-0"><?php for ($number = 1; $number <= $pages; $number++): ?><li class="page-item <?= $number === $page ? 'active' : ''; ?>"><a class="page-link" href="<?= app_url('/prodi') . '?' . http_build_query(['search' => $search, 'page' => $number]); ?>"><?= $number; ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
</div>
