<?php

namespace App\Controllers;

use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Repositories/ProdiRepository.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';

class MahasiswaController extends BaseController
{
    private const PER_PAGE = 10;

    public function __construct(
        private MahasiswaRepository $repository,
        private ProdiRepository $prodiRepository,
        private MahasiswaService $service
    ) {
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = $this->repository->count($search);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $mahasiswa = $this->repository->all($search, self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        $this->view('Mahasiswa/index', 'Data Mahasiswa | SI Akademik', compact('mahasiswa', 'search', 'page', 'pages', 'total'));
    }

    public function create(): void
    {
        $prodi = $this->prodiRepository->all();
        $this->view('Mahasiswa/create', 'Tambah Mahasiswa | SI Akademik', compact('prodi'));
    }

    public function edit(string $id): void
    {
        $item = $this->repository->find((int) $id);
        if ($item === null) {
            http_response_code(404);
            echo '404 - Mahasiswa tidak ditemukan';
            return;
        }

        $prodi = $this->prodiRepository->all();
        $this->view('Mahasiswa/edit', 'Edit Mahasiswa | SI Akademik', compact('item', 'prodi'));
    }

    public function show(string $id): void
    {
        $item = $this->repository->find((int) $id);
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
        $result = $this->service->create($_POST);
        if (!$result['success']) {
            $this->redirectWithFlash('danger', $result['message'], '/mahasiswa/create');
        }

        $this->redirectWithFlash('success', $result['message'], '/mahasiswa');
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $result = $this->service->update($id, $_POST);
        if (!$result['success']) {
            $path = isset($result['errors']['_global'])
                ? '/mahasiswa'
                : '/mahasiswa/' . $id . '/edit';
            $this->redirectWithFlash('danger', $result['message'], $path);
        }

        $this->redirectWithFlash('success', $result['message'], '/mahasiswa');
    }

    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->repository->find($id) === null) {
            $this->redirectWithFlash('danger', 'Data mahasiswa tidak ditemukan.', '/mahasiswa');
        }

        $this->repository->delete($id);
        $this->redirectWithFlash('success', 'Data mahasiswa berhasil dihapus.', '/mahasiswa');
    }

    private function redirectWithFlash(string $type, string $message, string $path): never
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        $this->redirect(app_url($path));
    }
}
