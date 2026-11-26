<?php include __DIR__ . '/../partials/data-list-styles.php'; ?>

<section class="page-heading rounded-4 p-3 p-lg-4 mb-3 d-flex flex-column justify-content-center">
    <p class="text-uppercase text-success small fw-bold mb-2">Portal Akademik</p>
    <h1 class="h2 fw-bold mb-2">Mata Kuliah</h1>
    <p class="text-secondary mb-0">Kelola mata kuliah dan program studi terkait.</p>
</section>

<div class="row row-cols-1 row-cols-sm-2 g-3 mb-4">
    <div class="col">
        <section class="card metric-card h-100 rounded-3 border-top border-4 border-primary">
            <div class="card-body p-3">
                <div class="small text-secondary mb-2">Total Mata Kuliah</div>
                <div class="metric-value fs-2 fw-bold"><?= (int) $totalAll; ?></div>
                <div class="small text-secondary">Mata kuliah terdaftar</div>
            </div>
        </section>
    </div>
    <div class="col">
        <section class="card metric-card h-100 rounded-3 border-top border-4 border-success">
            <div class="card-body p-3">
                <div class="small text-secondary mb-2">Hasil Ditampilkan</div>
                <div class="metric-value fs-2 fw-bold"><?= (int) $total; ?></div>
                <div class="small text-secondary">Menyesuaikan kata pencarian</div>
            </div>
        </section>
    </div>
</div>

<section class="card border-0 rounded-4 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 p-3 p-lg-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h2 class="h4 mb-1">Daftar Mata Kuliah</h2>
                <p class="text-secondary small mb-0">Cari berdasarkan kode, nama, atau program studi.</p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-lg-end">
                <form class="search-box" method="GET" action="<?= app_url('/matakuliah'); ?>">
                    <label for="search" class="visually-hidden">Cari kode, nama, atau program studi</label>
                    <input
                        id="search"
                        class="form-control"
                        type="search"
                        name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Cari mata kuliah..."
                    >
                </form>
                <a class="btn btn-success text-nowrap" href="<?= app_url('/matakuliah/create'); ?>">Tambah Mata Kuliah</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table data-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col" class="ps-4">No.</th>
                    <th scope="col">Kode</th>
                    <th scope="col">Nama Mata Kuliah</th>
                    <th scope="col">SKS</th>
                    <th scope="col">Program Studi</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td class="ps-4 text-secondary"><?= (int) (($page - 1) * $perPage + $index + 1); ?></td>
                        <td class="fw-semibold text-nowrap"><?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= (int) $item['sks']; ?></td>
                        <td><?= htmlspecialchars($item['prodi_nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center text-nowrap">
                            <a class="btn btn-sm btn-outline-success" href="<?= app_url('/matakuliah/' . (int) $item['id'] . '/edit'); ?>">Edit</a>
                            <form class="d-inline" action="<?= app_url('/matakuliah/destroy'); ?>" method="POST" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($items === []): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">Data mata kuliah tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <small class="text-secondary">Menampilkan <?= count($items); ?> dari <?= (int) $total; ?> mata kuliah</small>
        <?php if ($pages > 1): ?>
            <nav aria-label="Navigasi halaman mata kuliah">
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($number = 1; $number <= $pages; $number++): ?>
                        <li class="page-item <?= $number === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= app_url('/matakuliah') . '?' . http_build_query(['search' => $search, 'page' => $number]); ?>"><?= $number; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>
