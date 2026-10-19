<?php

namespace App\Controllers;

use App\Core\AppLogger;
use App\Models\Mahasiswa;
use App\Repositories\MahasiswaRepository;
use App\Services\MahasiswaService;
use JsonException;
use PDOException;

require_once __DIR__ . '/../Core/AppLogger.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';

class MahasiswaApiController
{
    public function __construct(
        private MahasiswaRepository $repository,
        private MahasiswaService $service
    ) {
    }

    public function index(): void
    {
        try {
            $id = $_GET['id'] ?? null;
            if ($id !== null) {
                if (!is_scalar($id)) {
                    $this->respond(400, false, 'ID mahasiswa harus berupa bilangan positif.', null);

                    return;
                }

                $validatedId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($validatedId === false) {
                    $this->respond(400, false, 'ID mahasiswa harus berupa bilangan positif.', null);

                    return;
                }

                $mahasiswa = $this->repository->find($validatedId);
                if ($mahasiswa === null) {
                    $this->respond(404, false, 'Data mahasiswa tidak ditemukan.', null);

                    return;
                }

                $this->respond(200, true, 'Data mahasiswa berhasil diambil.', $this->toArray($mahasiswa));

                return;
            }

            $total = $this->repository->count();
            $mahasiswa = $this->repository->all('', max(1, $total));
            $data = array_map(fn (Mahasiswa $item): array => $this->toArray($item), $mahasiswa);
            $this->respond(200, true, 'Data mahasiswa berhasil diambil.', $data);
        } catch (PDOException $exception) {
            AppLogger::error('API gagal mengambil data mahasiswa', $exception);
            $this->respond(500, false, 'Data mahasiswa gagal diambil.', null);
        }
    }

    public function store(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!preg_match('/^application\/json(?:\s*;|$)/i', $contentType)) {
            $this->respond(415, false, 'Content-Type harus application/json.', null);

            return;
        }

        try {
            $decoded = json_decode(file_get_contents('php://input'), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $this->respond(400, false, 'Body harus berisi JSON yang valid.', null);

            return;
        }

        if (!is_object($decoded)) {
            $this->respond(400, false, 'Body JSON harus berupa object.', null);

            return;
        }

        $input = get_object_vars($decoded);
        if (!array_key_exists('prodi_id', $input) || !array_key_exists('angkatan', $input)) {
            $this->respond(
                422,
                false,
                'Field prodi_id dan angkatan wajib dikirim karena diwajibkan oleh skema database.',
                null,
                ['required_fields' => ['nim', 'nama', 'email', 'prodi_id', 'angkatan']]
            );

            return;
        }

        $result = $this->service->create($input);
        if (!$result['success']) {
            $status = isset($result['errors']['_global']) ? 500 : 422;
            $this->respond($status, false, $result['message'], null, ['errors' => $result['errors']]);

            return;
        }

        try {
            $mahasiswa = $this->repository->findByNim(trim((string) $input['nim']));
        } catch (PDOException $exception) {
            AppLogger::error('API gagal mengambil data mahasiswa yang baru dibuat', $exception);
            $this->respond(500, false, 'Data tersimpan, tetapi gagal dibaca kembali.', null);

            return;
        }

        if ($mahasiswa === null) {
            $this->respond(500, false, 'Data tersimpan, tetapi gagal dibaca kembali.', null);

            return;
        }

        $this->respond(201, true, 'Data mahasiswa berhasil ditambahkan.', $this->toArray($mahasiswa));
    }

    /** @return array{id: int|null, nim: string, nama: string, email: string} */
    private function toArray(Mahasiswa $mahasiswa): array
    {
        return [
            'id' => $mahasiswa->getId(),
            'nim' => $mahasiswa->getNim(),
            'nama' => $mahasiswa->getNama(),
            'email' => $mahasiswa->getEmail(),
        ];
    }

    private function respond(
        int $status,
        bool $success,
        string $message,
        array|null $data,
        array $extra = []
    ): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            ['success' => $success, 'message' => $message, 'data' => $data] + $extra,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }
}
