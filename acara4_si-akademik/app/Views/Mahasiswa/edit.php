<?php
$pageTitle = 'Edit Mahasiswa';

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Edit Mahasiswa</h1>
    <a href="<?= htmlspecialchars($baseUrl . '?page=index', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Kembali</a>
</div>

<?php if ($flashMessage !== null): ?>
    <div class="alert alert-warning" role="alert"><?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="<?= htmlspecialchars($baseUrl . '?page=update', ENT_QUOTES, 'UTF-8'); ?>" method="POST">
            <input type="hidden" name="old_nim" value="<?= htmlspecialchars($mahasiswa->getNim(), ENT_QUOTES, 'UTF-8'); ?>">

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($mahasiswa->getNim(), ENT_QUOTES, 'UTF-8'); ?>" pattern="[0-9]{2,}" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($mahasiswa->getNama(), ENT_QUOTES, 'UTF-8'); ?>" minlength="3" required>
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <input type="text" class="form-control" id="prodi" name="prodi" value="<?= htmlspecialchars($mahasiswa->getProdi(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= htmlspecialchars($baseUrl . '?page=index', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
