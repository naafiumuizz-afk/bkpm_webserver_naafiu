<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\MahasiswaModel;

require_once __DIR__ . '/../Models/MahasiswaModel.php';

class MahasiswaController
{
    private const PER_PAGE = 10;

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = MahasiswaModel::count($search);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $mahasiswa = MahasiswaModel::all($search, self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        $this->render('index', 'Data Mahasiswa | SI Akademik', compact('mahasiswa', 'search', 'page', 'pages', 'total'));
    }

    public function create(): void
    {
        $prodi = $this->programStudi();
        $this->render('create', 'Tambah Mahasiswa | SI Akademik', compact('prodi'));
    }

    public function edit(string $id): void
    {
        $item = MahasiswaModel::find((int) $id);
        if ($item === null) {
            http_response_code(404);
            echo '404 - Mahasiswa tidak ditemukan';
            return;
        }

        $prodi = $this->programStudi();
        $this->render('edit', 'Edit Mahasiswa | SI Akademik', compact('item', 'prodi'));
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
        $data = $this->input();
        $error = $this->validate($data);
        if ($error !== null) {
            $this->redirectWithFlash('danger', $error, '/mahasiswa/create');
        }
        if (MahasiswaModel::nimExists($data['nim'])) {
            $this->redirectWithFlash('danger', 'NIM tersebut sudah terdaftar.', '/mahasiswa/create');
        }

        MahasiswaModel::create($data['nim'], $data['nama'], $data['email'], $data['prodi_id'], $data['angkatan']);
        $this->redirectWithFlash('success', 'Data mahasiswa berhasil ditambahkan.', '/mahasiswa');
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $item = MahasiswaModel::find($id);
        if ($item === null) {
            $this->redirectWithFlash('danger', 'Data mahasiswa tidak ditemukan.', '/mahasiswa');
        }

        $data = $this->input();
        $error = $this->validate($data);
        if ($error !== null) {
            $this->redirectWithFlash('danger', $error, '/mahasiswa/' . $id . '/edit');
        }
        if (MahasiswaModel::nimExists($data['nim'], $id)) {
            $this->redirectWithFlash('danger', 'NIM tersebut sudah digunakan mahasiswa lain.', '/mahasiswa/' . $id . '/edit');
        }

        MahasiswaModel::update($id, $data['nim'], $data['nama'], $data['email'], $data['prodi_id'], $data['angkatan']);
        $this->redirectWithFlash('success', 'Data mahasiswa berhasil diperbarui.', '/mahasiswa');
    }

    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (MahasiswaModel::find($id) === null) {
            $this->redirectWithFlash('danger', 'Data mahasiswa tidak ditemukan.', '/mahasiswa');
        }

        MahasiswaModel::delete($id);
        $this->redirectWithFlash('success', 'Data mahasiswa berhasil dihapus.', '/mahasiswa');
    }

    private function input(): array
    {
        return [
            'nim' => trim($_POST['nim'] ?? ''),
            'nama' => trim($_POST['nama'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
            'angkatan' => (int) ($_POST['angkatan'] ?? 0),
        ];
    }

    private function validate(array $data): ?string
    {
        if ($data['nim'] === '' || mb_strlen($data['nim']) > 20) {
            return 'NIM wajib diisi dan maksimal 20 karakter.';
        }
        if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) {
            return 'Nama wajib diisi dan maksimal 100 karakter.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 100) {
            return 'Email tidak valid atau melebihi 100 karakter.';
        }
        if ($data['angkatan'] < 2000 || $data['angkatan'] > 2100) {
            return 'Angkatan harus antara 2000 dan 2100.';
        }

        $statement = Database::connection()->prepare('SELECT 1 FROM prodi WHERE id = :id');
        $statement->execute(['id' => $data['prodi_id']]);
        if ($statement->fetchColumn() === false) {
            return 'Program studi wajib dipilih dan harus valid.';
        }

        return null;
    }

    private function programStudi(): array
    {
        return Database::connection()->query('SELECT id, kode, nama FROM prodi ORDER BY nama')->fetchAll();
    }

    private function render(string $view, string $pageTitle, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../Views/Mahasiswa/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    private function redirectWithFlash(string $type, string $message, string $path): never
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        header('Location: ' . app_url($path));
        exit;
    }
}
