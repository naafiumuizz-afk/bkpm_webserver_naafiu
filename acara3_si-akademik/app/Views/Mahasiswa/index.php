<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5">
        <header class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase text-secondary small fw-semibold mb-2">Sistem Akademik</p>
                <h1 class="h2 mb-2">Daftar Mahasiswa</h1>
            </div>
            <a href="create.php" class="btn btn-primary">Tambah Mahasiswa</a>
        </header>

        <section class="card border-0 shadow-sm" aria-labelledby="student-list-title">
            <div class="card-header bg-white border-0 px-4 pt-4 pb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h2 id="student-list-title" class="h5 mb-0">Data Mahasiswa</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Daftar mahasiswa beserta program studi dan aksi yang tersedia.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Program Studi</th>
                            <th scope="col" class="pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-medium">2023001</td>
                            <td>Andi Pratama</td>
                            <td>Teknik Informatika</td>
                            <td class="pe-4 text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1" disabled>Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" disabled>Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium">2023002</td>
                            <td>Siti Rahma</td>
                            <td>Sistem Informasi</td>
                            <td class="pe-4 text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1" disabled>Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" disabled>Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 px-4 py-3">
            </div>
        </section>
    </main>
</body>
</html>