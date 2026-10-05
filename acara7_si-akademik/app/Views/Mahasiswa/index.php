<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Daftar Mahasiswa</h1>
    <a href="<?= app_url('/mahasiswa/create'); ?>" class="btn btn-primary">Tambah Mahasiswa</a>
</div>

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
                <?php foreach ($mahasiswa as $index => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($item->getStatus(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center">
                            <a href="<?= app_url('/mahasiswa/' . $item->getId()); ?>" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
