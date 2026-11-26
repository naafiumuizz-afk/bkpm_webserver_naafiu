<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .login-card {
        width: 100%;
        max-width: 356px;
    }

    .login-card .card-body {
        padding: 20px;
    }
</style>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 d-flex justify-content-center">
            <div class="card login-card shadow-sm border-0">
            <div class="card-body">
                <?php if (!empty($_SESSION['flash'])): ?>
                    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                 <div class="text-center mb-4">
                    <h2 class="h6 fw-bold text-primary mb-2">
                        Sistem Informasi Akademik
                    </h2>
                    <h1 class="h3 mb-0">Login</h1>
                </div>

                <form action="<?= app_url('/login'); ?>" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-muted small text-center mt-3 mb-0">Demo: admin / admin123</p>
            </div>
        </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
