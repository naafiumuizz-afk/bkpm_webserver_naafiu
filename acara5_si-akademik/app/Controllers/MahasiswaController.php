<?php

namespace App\Controllers;

use App\Models\Mahasiswa;

require_once __DIR__ . '/../Models/Mahasiswa.php';

class MahasiswaController
{
    /** @return list<Mahasiswa> */
    private function data(): array
    {
        $sampleData = [
            'E41230001' => new Mahasiswa('E41230001', 'Andi Setiawan', 'Teknik Informatika'),
            'E41230002' => new Mahasiswa('E41230002', 'Siti Rahma', 'Manajemen Informatika'),
            'E41224003' => new Mahasiswa('E41224003', 'Budi Santoso', 'Teknik Komputer'),
            'E41230004' => new Mahasiswa('E41230004', 'Dewi Lestari', 'Manajemen Informatika'),
            'E41230005' => new Mahasiswa('E41230005', 'Rizky Pratama', 'Teknik Komputer'),
        ];

        $_SESSION['mahasiswa'] = array_replace($sampleData, $_SESSION['mahasiswa'] ?? []);

        return array_values($_SESSION['mahasiswa']);
    }

    private function find(string $nim): ?Mahasiswa
    {
        $this->data();
        $nim = strtoupper(trim($nim));

        return $_SESSION['mahasiswa'][$nim] ?? null;
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
        $error = null;
        $values = ['nim' => '', 'nama' => '', 'prodi' => ''];
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/create.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function edit(string $id = ''): void
    {
        $nim = $id !== '' ? $id : ($_GET['id'] ?? '');
        $item = $this->find($nim);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Mahasiswa tidak ditemukan';
            return;
        }

        $originalNim = $item->getNim();
        $error = null;
        $pageTitle = 'Edit Mahasiswa | SI Akademik';
        $values = [
            'nim' => $item->getNim(),
            'nama' => $item->getNama(),
            'prodi' => $item->getProdi(),
        ];
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/edit.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
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
        $values = compact('nim', 'nama', 'prodi');

        if ($nim === '' || $nama === '' || $prodi === '') {
            $error = 'NIM, nama, dan program studi wajib diisi.';
        } elseif ($this->find($nim) !== null) {
            $error = 'NIM sudah terdaftar.';
        }

        if (isset($error)) {
            http_response_code(422);
            $pageTitle = 'Tambah Mahasiswa | SI Akademik';
            ob_start();
            require __DIR__ . '/../Views/Mahasiswa/create.php';
            $content = ob_get_clean();
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        $item = new Mahasiswa($nim, $nama, $prodi);
        $_SESSION['mahasiswa'][$item->getNim()] = $item;

        header('Location: ' . app_url('/mahasiswa'));
        exit;
    }

    public function update(): void
    {
        $originalNim = strtoupper(trim($_POST['id'] ?? ''));
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');
        $values = compact('nim', 'nama', 'prodi');
        $item = $this->find($originalNim);

        if ($item === null) {
            http_response_code(404);
            echo '404 - Mahasiswa tidak ditemukan';
            return;
        }

        if ($nim === '' || $nama === '' || $prodi === '') {
            $error = 'NIM, nama, dan program studi wajib diisi.';
        } elseif (strtoupper($nim) !== $originalNim && $this->find($nim) !== null) {
            $error = 'NIM sudah terdaftar.';
        }

        if (isset($error)) {
            http_response_code(422);
            $pageTitle = 'Edit Mahasiswa | SI Akademik';
            ob_start();
            require __DIR__ . '/../Views/Mahasiswa/edit.php';
            $content = ob_get_clean();
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        unset($_SESSION['mahasiswa'][$originalNim]);
        $updated = new Mahasiswa($nim, $nama, $prodi);
        $_SESSION['mahasiswa'][$updated->getNim()] = $updated;

        header('Location: ' . app_url('/mahasiswa'));
        exit;
    }

}