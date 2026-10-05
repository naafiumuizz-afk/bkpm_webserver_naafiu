<?php
$pageTitle = $pageTitle ?? 'Sistem Akademik';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<main class="container py-4">
    <?php if (isset($content) && $content !== ''): ?>
        <?= $content; ?>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
