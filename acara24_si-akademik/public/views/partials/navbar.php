<?php
$baseUrl = $baseUrl ?? rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
?>
<style>
    .dashboard-navbar {
        background: linear-gradient(110deg, #102a43, #176b5b);
    }

    .dashboard-navbar .container {
        max-width: 1200px;
    }

    .dashboard-navbar .navbar-brand {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .dashboard-navbar .brand-mark {
        width: 2.25rem;
        height: 2.25rem;
        display: inline-grid;
        place-items: center;
        border-radius: .7rem;
        background: rgba(255, 255, 255, .16);
        font-weight: 700;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark dashboard-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= app_url(!empty($_SESSION['logged_in']) ? '/dashboard' : '/login'); ?>">
            <span class="brand-mark" aria-hidden="true">S</span>
            <span>SI Akademik</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!empty($_SESSION['logged_in'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= app_url('/dashboard'); ?>">Mahasiswa</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-sm btn-outline-light" href="<?= app_url('/logout'); ?>">Logout</a>
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
