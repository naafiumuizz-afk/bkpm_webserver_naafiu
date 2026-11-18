<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= app_url('/dashboard'); ?>">SI Akademik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= app_url('/dashboard'); ?>">Beranda</a>
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
                <?php if (!empty($_SESSION['logged_in'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('/logout'); ?>">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= app_url('/login'); ?>">Login</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="#">Dosen</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
