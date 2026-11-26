<footer class="border-top mt-4 py-3">
    <div class="container text-center text-body-secondary">
        &copy; <?= date('Y'); ?> Sistem Akademik
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    if (document.body.dataset.role !== 'admin') {
        document.querySelectorAll('[data-admin-only]').forEach((element) => {
            element.hidden = true;
        });
    }
</script>
</body>
</html>