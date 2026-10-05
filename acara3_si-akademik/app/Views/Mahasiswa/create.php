<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <header class="mb-4">
                    <p class="text-uppercase text-secondary small fw-semibold mb-2">Sistem Akademik</p>
                    <h1 class="h2 mb-2">Tambah Mahasiswa</h1>
                    <p class="text-secondary mb-0">Lengkapi informasi berikut untuk data mahasiswa.</p>
                </header>

                <section class="card border-0 shadow-sm" aria-labelledby="student-form-title">
                    <div class="card-body p-4 p-md-5">
                        <h2 id="student-form-title" class="h5 mb-4">Informasi Mahasiswa</h2>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM</label>
                                <input type="text" id="nim" name="nim" class="form-control" placeholder="Contoh: 2023001" autocomplete="off" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                            </div>

                            <div class="mb-4">
                                <label for="prodi" class="form-label">Program Studi</label>
                                <select id="prodi" name="prodi" class="form-select" required>
                                    <option value="" selected disabled>Pilih program studi</option>
                                    <option value="Teknik Informatika">Teknik Informatika</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Teknik Komputer">Teknik Komputer</option>
                                </select>
                            </div>

                            <div class="d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">
                                <a href="index.php" class="btn btn-outline-secondary">Kembali ke Daftar</a>
                                <button type="submit" class="btn btn-primary">Simpan Mahasiswa</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>


</body>
</html>