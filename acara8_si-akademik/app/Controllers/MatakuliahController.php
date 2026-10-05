<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\MatakuliahModel;

require_once __DIR__ . '/../Models/MatakuliahModel.php';

class MatakuliahController
{
    private const PER_PAGE = 10;

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = MatakuliahModel::count($search);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $items = MatakuliahModel::all($search, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
        $this->render('index', 'Data Mata Kuliah | SI Akademik', compact('items', 'search', 'page', 'pages', 'total'));
    }

    public function create(): void
    {
        $prodi = $this->programStudi();
        $this->render('create', 'Tambah Mata Kuliah | SI Akademik', compact('prodi'));
    }

    public function edit(string $id): void
    {
        $item = MatakuliahModel::find((int) $id);
        if ($item === null) {
            $this->redirect('danger', 'Mata kuliah tidak ditemukan.', '/matakuliah');
        }
        $prodi = $this->programStudi();
        $this->render('edit', 'Edit Mata Kuliah | SI Akademik', compact('item', 'prodi'));
    }

    public function store(): void
    {
        $data = $this->input();
        $error = $this->validate($data);
        if ($error !== null) {
            $this->redirect('danger', $error, '/matakuliah/create');
        }
        if (MatakuliahModel::codeExists($data['kode'])) {
            $this->redirect('danger', 'Kode mata kuliah sudah digunakan.', '/matakuliah/create');
        }
        if (MatakuliahModel::nameExists($data['nama'])) {
            $this->redirect('danger', 'Nama mata kuliah sudah terdaftar.', '/matakuliah/create');
        }

        MatakuliahModel::create($data['kode'], $data['nama'], $data['sks'], $data['prodi_id']);
        $this->redirect('success', 'Mata kuliah berhasil ditambahkan.', '/matakuliah');
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (MatakuliahModel::find($id) === null) {
            $this->redirect('danger', 'Mata kuliah tidak ditemukan.', '/matakuliah');
        }
        $data = $this->input();
        $error = $this->validate($data);
        if ($error !== null) {
            $this->redirect('danger', $error, '/matakuliah/' . $id . '/edit');
        }
        if (MatakuliahModel::codeExists($data['kode'], $id)) {
            $this->redirect('danger', 'Kode mata kuliah sudah digunakan.', '/matakuliah/' . $id . '/edit');
        }
        if (MatakuliahModel::nameExists($data['nama'], $id)) {
            $this->redirect('danger', 'Nama mata kuliah sudah terdaftar.', '/matakuliah/' . $id . '/edit');
        }

        MatakuliahModel::update($id, $data['kode'], $data['nama'], $data['sks'], $data['prodi_id']);
        $this->redirect('success', 'Mata kuliah berhasil diperbarui.', '/matakuliah');
    }

    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (MatakuliahModel::find($id) === null) {
            $this->redirect('danger', 'Mata kuliah tidak ditemukan.', '/matakuliah');
        }
        MatakuliahModel::delete($id);
        $this->redirect('success', 'Mata kuliah berhasil dihapus.', '/matakuliah');
    }

    private function input(): array
    {
        return [
            'kode' => strtoupper(trim($_POST['kode'] ?? '')),
            'nama' => trim($_POST['nama'] ?? ''),
            'sks' => (int) ($_POST['sks'] ?? 0),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
        ];
    }

    private function validate(array $data): ?string
    {
        if ($data['kode'] === '' || strlen($data['kode']) > 10) {
            return 'Kode mata kuliah wajib diisi dan maksimal 10 karakter.';
        }
        if ($data['nama'] === '' || mb_strlen($data['nama']) > 150) {
            return 'Nama mata kuliah wajib diisi dan maksimal 150 karakter.';
        }
        if ($data['sks'] < 1 || $data['sks'] > 24) {
            return 'SKS harus di antara 1 dan 24.';
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
        require __DIR__ . '/../Views/Matakuliah/' . $view . '.php';
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
