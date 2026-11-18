<?php

namespace App\Controllers;

use App\Models\ProdiModel;
use PDOException;

require_once __DIR__ . '/../Models/ProdiModel.php';

class ProdiController
{
    private const PER_PAGE = 10;

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = ProdiModel::count($search);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $items = ProdiModel::all($search, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
        $this->render('index', 'Data Program Studi | SI Akademik', compact('items', 'search', 'page', 'pages', 'total'));
    }

    public function create(): void
    {
        $this->render('create', 'Tambah Program Studi | SI Akademik');
    }

    public function edit(string $id): void
    {
        $item = ProdiModel::find((int) $id);
        if ($item === null) {
            $this->redirect('danger', 'Program studi tidak ditemukan.', '/prodi');
        }
        $this->render('edit', 'Edit Program Studi | SI Akademik', compact('item'));
    }

    public function store(): void
    {
        $data = $this->input();
        $error = $this->validate($data);
        if ($error !== null) {
            $this->redirect('danger', $error, '/prodi/create');
        }
        if (ProdiModel::codeExists($data['kode'])) {
            $this->redirect('danger', 'Kode program studi sudah digunakan.', '/prodi/create');
        }
        if (ProdiModel::nameExists($data['nama'])) {
            $this->redirect('danger', 'Nama program studi sudah terdaftar.', '/prodi/create');
        }

        ProdiModel::create($data['kode'], $data['nama']);
        $this->redirect('success', 'Program studi berhasil ditambahkan.', '/prodi');
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (ProdiModel::find($id) === null) {
            $this->redirect('danger', 'Program studi tidak ditemukan.', '/prodi');
        }
        $data = $this->input();
        $error = $this->validate($data);
        if ($error !== null) {
            $this->redirect('danger', $error, '/prodi/' . $id . '/edit');
        }
        if (ProdiModel::codeExists($data['kode'], $id)) {
            $this->redirect('danger', 'Kode program studi sudah digunakan.', '/prodi/' . $id . '/edit');
        }
        if (ProdiModel::nameExists($data['nama'], $id)) {
            $this->redirect('danger', 'Nama program studi sudah terdaftar.', '/prodi/' . $id . '/edit');
        }

        ProdiModel::update($id, $data['kode'], $data['nama']);
        $this->redirect('success', 'Program studi berhasil diperbarui.', '/prodi');
    }

    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (ProdiModel::find($id) === null) {
            $this->redirect('danger', 'Program studi tidak ditemukan.', '/prodi');
        }
        try {
            ProdiModel::delete($id);
        } catch (PDOException) {
            $this->redirect('danger', 'Program studi masih digunakan mahasiswa atau mata kuliah.', '/prodi');
        }
        $this->redirect('success', 'Program studi berhasil dihapus.', '/prodi');
    }

    private function input(): array
    {
        return ['kode' => strtoupper(trim($_POST['kode'] ?? '')), 'nama' => trim($_POST['nama'] ?? '')];
    }

    private function validate(array $data): ?string
    {
        if ($data['kode'] === '' || strlen($data['kode']) > 10) {
            return 'Kode program studi wajib diisi dan maksimal 10 karakter.';
        }
        if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) {
            return 'Nama program studi wajib diisi dan maksimal 100 karakter.';
        }

        return null;
    }

    private function render(string $view, string $pageTitle, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../Views/Prodi/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    private function redirect(string $type, string $message, string $path): never
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        header('Location: ' . app_url($path));
        exit;
    }
}
