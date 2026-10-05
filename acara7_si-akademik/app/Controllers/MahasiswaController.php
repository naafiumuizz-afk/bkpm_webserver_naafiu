<?php

namespace App\Controllers;

use App\Models\MahasiswaModel;

require_once __DIR__ . '/../Models/MahasiswaModel.php';

class MahasiswaController
{
    public function index(): void
    {
        $mahasiswa = MahasiswaModel::all();
        $pageTitle = 'Data Mahasiswa | SI Akademik';
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $prodi = \App\Core\Database::connection()
            ->query('SELECT id, kode, nama FROM prodi ORDER BY nama')
            ->fetchAll();
        $pageTitle = 'Tambah Mahasiswa | SI Akademik';
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/create.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function show(string $id): void
    {
        $item = MahasiswaModel::find((int) $id);
        if ($item === null) {
            http_response_code(404);
            echo '404 - Mahasiswa tidak ditemukan';
            return;
        }

        echo '<h1>Detail Mahasiswa</h1>';
        echo '<p>NIM: ' . htmlspecialchars($item->getNim(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Nama: ' . htmlspecialchars($item->getNama(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Program Studi: ' . htmlspecialchars($item->getProdi(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><a href="' . app_url('/mahasiswa') . '">Kembali ke daftar</a></p>';
    }

    public function store(): void
    {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $prodiId = (int) ($_POST['prodi_id'] ?? 0);
        $angkatan = (int) ($_POST['angkatan'] ?? 0);

        if ($nim === '' || $nama === '' || $email === '' || $prodiId < 1 || $angkatan < 1) {
            http_response_code(422);
            echo '422 - NIM, nama, dan program studi wajib diisi';
            return;
        }

        $prodi = \App\Core\Database::connection()
            ->prepare('SELECT id FROM prodi WHERE id = :id');
        $prodi->execute(['id' => $prodiId]);
        if ($prodi->fetch() === false) {
            http_response_code(422);
            echo '422 - Program studi tidak valid';
            return;
        }

        MahasiswaModel::create($nim, $nama, $email, $prodiId, $angkatan);
        header('Location: ' . app_url('/mahasiswa'));
        exit;
    }
}