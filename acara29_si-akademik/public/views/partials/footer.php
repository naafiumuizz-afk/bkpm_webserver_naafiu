<footer class="border-top mt-4 py-3">
    <div class="container text-center text-body-secondary">
        &copy; <?= date('Y'); ?> Sistem Akademik
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.APP_API_URL = <?= json_encode(app_url('/api/mahasiswa'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
</script>
<script src="<?= htmlspecialchars(app_url('/public/views/app.js'), ENT_QUOTES, 'UTF-8'); ?>?v=5"></script>
<script>
    if (document.body.dataset.role !== 'admin') {
        document.querySelectorAll('[data-admin-only]').forEach((element) => {
            element.hidden = true;
        });
    }
</script>
</body>
</html>