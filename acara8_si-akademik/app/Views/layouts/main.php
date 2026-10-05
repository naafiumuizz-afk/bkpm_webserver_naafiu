<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Sistem Akademik';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main class="container py-4">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (isset($content) && $content !== ''): ?>
        <?= $content; ?>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
