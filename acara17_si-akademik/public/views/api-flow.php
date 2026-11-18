<section class="mb-4">
    <h1 class="h2">Alur API CRUD Buku</h1>
    <p class="text-muted">Halaman ini menampilkan alur request API melalui arsitektur MVC.</p>
</section>

<div class="row g-3 align-items-stretch mb-4">
    <?php
    $steps = [
        ['Client', 'Thunder Client mengirim HTTP request dan menerima JSON response.'],
        ['Front Controller', 'public/index.php menerima request yang diarahkan oleh .htaccess.'],
        ['Router', 'app/Core/Router.php mencocokkan URL dan HTTP method.'],
        ['Controller', 'BukuApiController menangani request dan menyusun response.'],
        ['Model & Repository', 'Buku dan BukuRepository memvalidasi data serta menjalankan query PDO.'],
        ['MySQL', 'Database si_akademik menyimpan dan mengembalikan data buku.'],
    ];
    ?>
    <?php foreach ($steps as $index => [$title, $description]): ?>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <span class="badge text-bg-primary mb-2"><?= $index + 1; ?></span>
                    <h2 class="h5"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="mb-0"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>

<section class="card shadow-sm border-0">
    <div class="card-body">
        <h2 class="h5">Endpoint untuk pengujian</h2>
        <p class="mb-2">Gunakan base URL berikut pada Thunder Client:</p>
        <code><?= htmlspecialchars($baseUrl . '/api/buku', ENT_QUOTES, 'UTF-8'); ?></code>
        <hr>
        <ul class="mb-0">
            <li><strong>GET</strong> — tampilkan semua buku: <code><?= htmlspecialchars($baseUrl . '/api/buku', ENT_QUOTES, 'UTF-8'); ?></code></li>
            <li><strong>GET</strong> — tampilkan satu buku: <code><?= htmlspecialchars($baseUrl . '/api/buku/1', ENT_QUOTES, 'UTF-8'); ?></code></li>
            <li><strong>POST</strong> — tambah buku ke endpoint collection.</li>
            <li><strong>PUT</strong> — ubah buku pada endpoint item, misalnya <code>/api/buku/1</code>.</li>
            <li><strong>DELETE</strong> — hapus buku pada endpoint item, misalnya <code>/api/buku/1</code>.</li>
        </ul>
    </div>
</section>
