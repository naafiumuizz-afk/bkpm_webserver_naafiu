<?php

namespace App\Controllers;

use App\Core\AppLogger;
use App\Models\Buku;
use App\Repositories\BukuRepository;
use JsonException;
use PDOException;

require_once __DIR__ . '/../Core/AppLogger.php';
require_once __DIR__ . '/../Models/Buku.php';
require_once __DIR__ . '/../Repositories/BukuRepository.php';

class BukuApiController
{
    public function __construct(private BukuRepository $repository)
    {
    }

    public function index(): void
    {
        try {
            $data = array_map(
                static fn (Buku $buku): array => $buku->toArray(),
                $this->repository->all()
            );
            $this->respond(200, true, 'Data buku berhasil diambil.', $data);
        } catch (PDOException $exception) {
            AppLogger::error('API gagal mengambil data buku', $exception);
            $this->respond(500, false, 'Data buku gagal diambil.');
        }
    }

    public function show(int $id): void
    {
        try {
            $buku = $this->repository->find($id);
            if ($buku === null) {
                $this->respond(404, false, 'Data buku tidak ditemukan.');

                return;
            }

            $this->respond(200, true, 'Data buku berhasil diambil.', $buku->toArray());
        } catch (PDOException $exception) {
            AppLogger::error('API gagal mengambil data buku', $exception);
            $this->respond(500, false, 'Data buku gagal diambil.');
        }
    }

    public function store(): void
    {
        $input = $this->readInput();
        if ($input === null) {
            return;
        }

        try {
            $buku = $this->makeBuku($input);
            if ($this->repository->existsByKode($buku->kode)) {
                $this->respond(422, false, 'Kode buku sudah digunakan.', null, ['errors' => ['kode' => 'Kode buku sudah digunakan.']]);

                return;
            }
            $id = $this->repository->create($buku);
            $created = $this->repository->find($id);
            if ($created === null) {
                $this->respond(500, false, 'Data tersimpan, tetapi gagal dibaca kembali.');

                return;
            }

            $this->respond(201, true, 'Data buku berhasil ditambahkan.', $created->toArray());
        } catch (\InvalidArgumentException $exception) {
            $this->respond(422, false, $exception->getMessage());
        } catch (PDOException $exception) {
            if ((string) $exception->getCode() === '23000') {
                $this->respond(422, false, 'Kode buku sudah digunakan.', null, ['errors' => ['kode' => 'Kode buku sudah digunakan.']]);

                return;
            }
            AppLogger::error('API gagal menambahkan data buku', $exception);
            $this->respond(500, false, 'Data buku gagal disimpan.');
        }
    }

    public function update(int $id): void
    {
        $input = $this->readInput();
        if ($input === null) {
            return;
        }

        try {
            if ($this->repository->find($id) === null) {
                $this->respond(404, false, 'Data buku tidak ditemukan.');

                return;
            }
            $buku = $this->makeBuku($input, $id);
            if ($this->repository->existsByKode($buku->kode, $id)) {
                $this->respond(422, false, 'Kode buku sudah digunakan.', null, ['errors' => ['kode' => 'Kode buku sudah digunakan.']]);

                return;
            }

            $this->repository->update($buku);
            $updated = $this->repository->find($id);
            if ($updated === null) {
                $this->respond(500, false, 'Data diperbarui, tetapi gagal dibaca kembali.');

                return;
            }

            $this->respond(200, true, 'Data buku berhasil diperbarui.', $updated->toArray());
        } catch (\InvalidArgumentException $exception) {
            $this->respond(422, false, $exception->getMessage());
        } catch (PDOException $exception) {
            if ((string) $exception->getCode() === '23000') {
                $this->respond(422, false, 'Kode buku sudah digunakan.', null, ['errors' => ['kode' => 'Kode buku sudah digunakan.']]);

                return;
            }
            AppLogger::error('API gagal memperbarui data buku', $exception);
            $this->respond(500, false, 'Data buku gagal diperbarui.');
        }
    }

    public function destroy(int $id): void
    {
        try {
            $buku = $this->repository->find($id);
            if ($buku === null) {
                $this->respond(404, false, 'Data buku tidak ditemukan.');

                return;
            }

            $this->repository->delete($id);
            $this->respond(200, true, 'Data buku berhasil dihapus.', ['id' => $id]);
        } catch (PDOException $exception) {
            AppLogger::error('API gagal menghapus data buku', $exception);
            $this->respond(500, false, 'Data buku gagal dihapus.');
        }
    }

    private function readInput(): ?array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!preg_match('/^application\/json(?:\s*;|$)/i', $contentType)) {
            $this->respond(415, false, 'Content-Type harus application/json.');

            return null;
        }

        try {
            $decoded = json_decode(file_get_contents('php://input'), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $this->respond(400, false, 'Body harus berisi JSON yang valid.');

            return null;
        }

        if (!is_object($decoded)) {
            $this->respond(400, false, 'Body JSON harus berupa object.');

            return null;
        }

        return get_object_vars($decoded);
    }

    /** @param array<string, mixed> $input */
    private function makeBuku(array $input, ?int $id = null): Buku
    {
        foreach (['kode', 'judul', 'penulis', 'penerbit', 'tahun_terbit'] as $field) {
            if (!isset($input[$field]) || !is_scalar($input[$field]) || trim((string) $input[$field]) === '') {
                throw new \InvalidArgumentException("Field {$field} wajib diisi.");
            }
        }

        $year = filter_var($input['tahun_terbit'], FILTER_VALIDATE_INT);
        if ($year === false) {
            throw new \InvalidArgumentException('Tahun terbit harus berupa bilangan bulat.');
        }

        return new Buku(
            trim((string) $input['kode']),
            trim((string) $input['judul']),
            trim((string) $input['penulis']),
            trim((string) $input['penerbit']),
            $year,
            $id
        );
    }

    private function respond(
        int $status,
        bool $success,
        string $message,
        array|null $data = null,
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
