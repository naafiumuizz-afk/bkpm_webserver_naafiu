<?php include __DIR__ . '/../partials/data-list-styles.php'; ?>

<section class="page-heading rounded-4 p-3 p-lg-4 mb-3 d-flex flex-column justify-content-center">
    <p class="text-uppercase text-success small fw-bold mb-2">Portal Akademik</p>
    <h1 class="h2 fw-bold mb-2">Data Mahasiswa</h1>
    <p class="text-secondary mb-0">Informasi mahasiswa tersaji dalam satu daftar yang mudah dicari.</p>
</section>

<div class="row row-cols-1 row-cols-sm-2 g-3 mb-4">
    <div class="col">
        <section class="card metric-card h-100 rounded-3 border-top border-4 border-primary">
            <div class="card-body p-3">
                <div class="small text-secondary mb-2">Total Mahasiswa</div>
                <div class="metric-value fs-2 fw-bold"><?= (int) $totalMahasiswa; ?></div>
                <div class="small text-secondary">Data mahasiswa terdaftar</div>
            </div>
        </section>
    </div>
    <div class="col">
        <section class="card metric-card h-100 rounded-3 border-top border-4 border-success">
            <div class="card-body p-3">
                <div class="small text-secondary mb-2">Hasil Ditampilkan</div>
                <div class="metric-value fs-2 fw-bold"><?= count($mahasiswa); ?></div>
                <div class="small text-secondary">Menyesuaikan kata pencarian</div>
            </div>
        </section>
    </div>
</div>

<section class="card border-0 rounded-4 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 p-3 p-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h4 mb-1">Daftar Mahasiswa</h2>
                <p class="text-secondary small mb-0">Cari berdasarkan NIM, nama, atau email.</p>
            </div>
            <form class="search-box" method="GET" action="<?= app_url('/dashboard'); ?>">
                <label for="search" class="visually-hidden">Cari berdasarkan NIM, nama, atau email</label>
                <input
                    id="search"
                    class="form-control"
                    type="search"
                    name="search"
                    value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Cari mahasiswa..."
                >
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table id="tabel-mahasiswa" class="table data-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col" class="ps-4">No.</th>
                    <th scope="col">NIM</th>
                    <th scope="col">Mahasiswa</th>
                    <th scope="col">Angkatan</th>
                    <th scope="col">Prodi</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswa as $index => $item): ?>
                    <?php $nama = $item->getNama(); ?>
                    <tr>
                        <td class="ps-4 text-secondary"><?= (int) (($page - 1) * $perPage + $index + 1); ?></td>
                        <td class="text-nowrap fw-semibold"><?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="student-avatar" aria-hidden="true"><?= htmlspecialchars(mb_strtoupper(mb_substr(trim($nama), 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($item->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getStatus(), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($mahasiswa === []): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">
                            <?= $search !== '' ? 'Tidak ada mahasiswa yang cocok dengan pencarian.' : 'Belum ada data mahasiswa.'; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <small class="text-secondary">Menampilkan <?= count($mahasiswa); ?> dari <?= (int) $total; ?> mahasiswa</small>
        <?php if ($pages > 1): ?>
            <nav aria-label="Navigasi halaman mahasiswa">
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($number = 1; $number <= $pages; $number++): ?>
                        <li class="page-item <?= $number === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= app_url('/dashboard') . '?' . http_build_query(['search' => $search, 'page' => $number]); ?>"><?= $number; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>
