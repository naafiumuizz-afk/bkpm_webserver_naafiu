<?php

namespace App\Controllers;

use App\Core\AppLogger;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;
use RuntimeException;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Repositories/ProdiRepository.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';

class MahasiswaController extends BaseController
{
    private const PER_PAGE = 10;
    private const MAX_PHOTO_SIZE = 2 * 1024 * 1024;

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
        $totalMahasiswa = $this->repository->count();
        $total = $this->repository->count($search);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $mahasiswa = $this->repository->all($search, self::PER_PAGE, ($page - 1) * self::PER_PAGE);
        $perPage = self::PER_PAGE;

        $dashboardLayout = true;
        $this->view(
            'Mahasiswa/index',
            'Data Mahasiswa | SI Akademik',
            compact('mahasiswa', 'search', 'page', 'pages', 'total', 'totalMahasiswa', 'perPage', 'dashboardLayout')
        );
    }

    public function create(): void
    {
        $prodi = $this->prodiRepository->all();
        $this->view('Mahasiswa/create', 'Tambah Mahasiswa | SI Akademik', compact('prodi'));
    }

    public function edit(?string $id = null): void
    {
        $id = $id ?? (is_scalar($_GET['id'] ?? null) ? (string) $_GET['id'] : '0');
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
        try {
            $photo = $this->storePhoto($_FILES['foto'] ?? null);
        } catch (RuntimeException $exception) {
            $this->redirectWithFlash('danger', $exception->getMessage(), '/mahasiswa/create');
        }

        $result = $this->service->create($_POST, $photo);
        if (!$result['success']) {
            if ($photo !== null && !$this->deletePhoto($photo)) {
                AppLogger::error('Gagal membersihkan foto mahasiswa yang tidak tersimpan', new RuntimeException($photo));
            }
            $this->redirectWithFlash('danger', $result['message'], '/mahasiswa/create');
        }

        $this->redirectWithFlash('success', $result['message'], '/mahasiswa');
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $existing = $this->repository->find($id);
        try {
            $photo = $this->storePhoto($_FILES['foto'] ?? null);
        } catch (RuntimeException $exception) {
            $this->redirectWithFlash('danger', $exception->getMessage(), '/mahasiswa/' . $id . '/edit');
        }

        $result = $this->service->update($id, $_POST, $photo);
        if (!$result['success']) {
            if ($photo !== null && !$this->deletePhoto($photo)) {
                AppLogger::error('Gagal membersihkan foto mahasiswa yang tidak tersimpan', new RuntimeException($photo));
            }
            $path = isset($result['errors']['_global'])
                ? '/mahasiswa'
                : '/mahasiswa/' . $id . '/edit';
            $this->redirectWithFlash('danger', $result['message'], $path);
        }

        $message = $result['message'];
        if ($photo !== null && $existing !== null && $existing->getFoto() !== null && !$this->deletePhoto($existing->getFoto())) {
            AppLogger::error('Gagal menghapus foto mahasiswa lama', new RuntimeException($existing->getFoto()));
            $this->redirectWithFlash('warning', $message . ' Namun, foto lama gagal dihapus dari penyimpanan.', '/mahasiswa');
        }

        $this->redirectWithFlash('success', $message, '/mahasiswa');
    }

    public function destroy(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $result = $this->service->delete($id);
        if (!$result['success']) {
            $this->redirectWithFlash('danger', $result['message'], '/mahasiswa');
        }

        $this->redirectWithFlash('success', $result['message'], '/mahasiswa');
    }

    private function redirectWithFlash(string $type, string $message, string $path): never
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        $this->redirect(app_url($path));
    }

    private function storePhoto(mixed $file): ?string
    {
        if ($file === null || (is_array($file) && ($file['error'] ?? null) === UPLOAD_ERR_NO_FILE)) {
            return null;
        }
        if (
            !is_array($file)
            || !isset($file['error'], $file['tmp_name'], $file['name'])
            || !is_int($file['error'])
            || !is_string($file['tmp_name'])
            || !is_string($file['name'])
        ) {
            throw new RuntimeException('Data foto tidak valid.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
                ? 'Ukuran foto maksimal 2MB.'
                : 'Foto gagal diunggah. Silakan coba kembali.');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Berkas foto tidak valid.');
        }
        $size = filesize($file['tmp_name']);
        if ($size === false || $size < 1 || $size > self::MAX_PHOTO_SIZE) {
            throw new RuntimeException('Ukuran foto maksimal 2MB.');
        }
        if (!function_exists('exif_imagetype')) {
            throw new RuntimeException('Fitur validasi foto belum tersedia pada konfigurasi PHP.');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $imageType = exif_imagetype($file['tmp_name']);
        $extensionsByImageType = [
            IMAGETYPE_JPEG => ['jpg', 'jpeg'],
            IMAGETYPE_PNG => ['png'],
        ];
        if (!isset($extensionsByImageType[$imageType]) || !in_array($extension, $extensionsByImageType[$imageType], true)) {
            throw new RuntimeException('Foto harus berupa gambar JPG atau PNG yang valid.');
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads';
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Folder penyimpanan foto tidak dapat dibuat.');
        }

        $newName = 'foto_' . bin2hex(random_bytes(16)) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $newName)) {
            throw new RuntimeException('Foto gagal disimpan. Silakan coba kembali.');
        }

        return $newName;
    }

    private function deletePhoto(?string $fileName): bool
    {
        if ($fileName === null || $fileName === '') {
            return true;
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads';
        if (basename($fileName) !== $fileName) {
            return false;
        }

        $path = $directory . '/' . $fileName;

        return !is_file($path) || unlink($path);
    }
}
