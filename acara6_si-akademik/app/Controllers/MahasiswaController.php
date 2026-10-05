<?php

namespace App\Controllers;

use App\Models\Mahasiswa;

require_once __DIR__ . '/../Models/Mahasiswa.php';

class MahasiswaController
{
    /** @return list<Mahasiswa> */
    private function data(): array
    {
        return [
            new Mahasiswa('E41230001', 'Andi Setiawan', 'Teknik Informatika'),
            new Mahasiswa('E41230002', 'Siti Rahma', 'Manajemen Informatika'),
            new Mahasiswa('E41224003', 'Budi Santoso', 'Teknik Komputer'),
        ];
    }

    public function index(): void
    {
        $mahasiswa = $this->data();
        $pageTitle = 'Data Mahasiswa | SI Akademik';
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $pageTitle = 'Tambah Mahasiswa | SI Akademik';
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/create.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function edit(): void
    {
        require __DIR__ . '/../Views/Mahasiswa/edit.php';
    }

    public function show(string $id): void
    {
        $mahasiswa = $this->data();
        $index = (int) $id - 1;

        if (!isset($mahasiswa[$index])) {
            http_response_code(404);
            echo '404 - Mahasiswa tidak ditemukan';
            return;
        }

        $item = $mahasiswa[$index];
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
        $prodi = trim($_POST['prodi'] ?? '');

        if ($nim === '' || $nama === '' || $prodi === '') {
            http_response_code(422);
            echo '422 - NIM, nama, dan program studi wajib diisi';
            return;
        }

        header('Location: ' . app_url('/mahasiswa'));
        exit;
    }

    public function update(): void
    {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        if ($nim === '' || $nama === '' || $prodi === '') {
            http_response_code(422);
            echo '422 - NIM, nama, dan program studi wajib diisi';
            return;
        }

        header('Location: ' . app_url('/mahasiswa'));
        exit;
    }
}