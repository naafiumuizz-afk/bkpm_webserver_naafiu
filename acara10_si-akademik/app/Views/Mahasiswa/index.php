<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Daftar Mahasiswa</h1>
    <a href="<?= app_url('/mahasiswa/create'); ?>" class="btn btn-primary">Tambah Mahasiswa</a>
</div>

<form class="row g-2 mb-3" method="GET" action="<?= app_url('/mahasiswa'); ?>">
    <div class="col-sm-8 col-md-5">
        <label for="search" class="visually-hidden">Cari nama atau NIM</label>
        <input class="form-control" type="search" id="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cari nama atau NIM">
    </div>
    <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Cari</button></div>
    <?php if ($search !== ''): ?><div class="col-auto"><a class="btn btn-outline-secondary" href="<?= app_url('/mahasiswa'); ?>">Reset</a></div><?php endif; ?>
</form>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-striped table-bordered mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">NIM</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Prodi</th>
                    <th scope="col">Angkatan</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswa as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getStatus(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center">
                            <a href="<?= app_url('/mahasiswa/' . $item->getId()); ?>" class="btn btn-sm btn-outline-info">Detail</a>
                            <a href="<?= app_url('/mahasiswa/' . $item->getId() . '/edit'); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form class="d-inline" action="<?= app_url('/mahasiswa/destroy'); ?>" method="POST" onsubmit="return confirm('Hapus data mahasiswa ini?')">
                                <input type="hidden" name="id" value="<?= (int) $item->getId(); ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($mahasiswa === []): ?>
                    <tr><td colspan="6" class="text-center py-4">Data mahasiswa tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Menampilkan <?= count($mahasiswa); ?> dari <?= $total; ?> data</small>
    <?php if ($pages > 1): ?>
        <nav aria-label="Navigasi halaman mahasiswa">
            <ul class="pagination pagination-sm mb-0">
                <?php for ($number = 1; $number <= $pages; $number++): ?>
                    <li class="page-item <?= $number === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="<?= app_url('/mahasiswa') . '?' . http_build_query(['search' => $search, 'page' => $number]); ?>"><?= $number; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
