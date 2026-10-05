<?php
ob_start();
?>
<h1 class="mb-4">Daftar Mahasiswa</h1>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-striped table-bordered mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">NIM</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Prodi</th>
                    <th scope="col">Angkatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswaList as $mahasiswa): ?>
                    <tr>
                        <td><?= htmlspecialchars($mahasiswa->getNim(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($mahasiswa->getNama(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($mahasiswa->getProdi(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($mahasiswa->getAngkatan(), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
