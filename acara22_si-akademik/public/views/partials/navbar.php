<?php
$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= app_url(!empty($_SESSION['logged_in']) ? '/dashboard' : '/login'); ?>">SI Akademik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!empty($_SESSION['logged_in'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/dashboard'); ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/mahasiswa'); ?>">Mahasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/prodi'); ?>">Program Studi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/matakuliah'); ?>">Mata Kuliah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/logout'); ?>">Logout</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/dosen'); ?>">Dosen</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/login'); ?>">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
