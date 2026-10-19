<?php

namespace App\Services;

use App\Core\AppLogger;
use App\Models\Mahasiswa;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use PDOException;

require_once __DIR__ . '/../Core/AppLogger.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Repositories/ProdiRepository.php';

class MahasiswaService
{
    public function __construct(
        private MahasiswaRepository $mahasiswaRepository,
        private ProdiRepository $prodiRepository
    ) {
    }

    /** @return array{success: bool, message: string, errors: array<string, string>} */
    public function create(array $input): array
    {
        $data = $this->normalizeInput($input);
        try {
            $errors = $this->validate($data);
            if ($errors !== []) {
                return $this->failure($errors);
            }
            if ($this->mahasiswaRepository->existsByNim($data['nim'])) {
                return $this->failure(['nim' => 'NIM tersebut sudah terdaftar.'], false);
            }

            $mahasiswa = new Mahasiswa(
                $data['nim'],
                $data['nama'],
                '',
                $data['email'],
                $data['prodi_id'],
                $data['angkatan']
            );
            $this->mahasiswaRepository->create($mahasiswa);
        } catch (PDOException $exception) {
            AppLogger::error('Gagal menambahkan mahasiswa', $exception);

            return $this->failure(['_global' => 'Silakan coba kembali.']);
        }

        return [
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan.',
            'errors' => [],
        ];
    }

    /** @return array{success: bool, message: string, errors: array<string, string>} */
    public function update(int $id, array $input): array
    {
        $data = $this->normalizeInput($input);
        try {
            $errors = $this->validate($data);
            if ($errors !== []) {
                return $this->failure($errors);
            }

            $mahasiswa = $this->mahasiswaRepository->find($id);
            if ($mahasiswa === null) {
                return $this->failure(['_global' => 'Data mahasiswa tidak ditemukan.'], false);
            }
            if ($this->mahasiswaRepository->existsByNim($data['nim'], $id)) {
                return $this->failure(['nim' => 'NIM tersebut sudah digunakan mahasiswa lain.']);
            }

            $mahasiswa->setNim($data['nim']);
            $mahasiswa->setNama($data['nama']);
            $mahasiswa->setEmail($data['email']);
            $mahasiswa->setProdiId($data['prodi_id']);
            $mahasiswa->setAngkatan($data['angkatan']);
            $this->mahasiswaRepository->update($mahasiswa);
        } catch (PDOException $exception) {
            AppLogger::error('Gagal memperbarui mahasiswa', $exception);

            return $this->failure(['_global' => 'Silakan coba kembali.']);
        }

        return [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui.',
            'errors' => [],
        ];
    }

    /** @return array{success: bool, message: string, errors: array<string, string>} */
    public function delete(int $id): array
    {
        try {
            if ($this->mahasiswaRepository->find($id) === null) {
                return $this->failure(['_global' => 'Data mahasiswa tidak ditemukan.'], false);
            }

            $this->mahasiswaRepository->delete($id);
        } catch (PDOException $exception) {
            AppLogger::error('Gagal menghapus mahasiswa', $exception);

            return $this->failure(['_global' => 'Silakan coba kembali.']);
        }

        return [
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus.',
            'errors' => [],
        ];
    }

    /**
     * @param array{nim: string, nama: string, email: string, prodi_id: int, angkatan: int} $data
     * @return array<string, string>
     */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['nim'] === '' || !ctype_digit($data['nim']) || mb_strlen($data['nim']) > 20) {
            $errors['nim'] = 'NIM wajib diisi, harus berupa angka, dan maksimal 20 karakter.';
        }
        if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) {
            $errors['nama'] = 'Nama wajib diisi dan maksimal 100 karakter.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 100) {
            $errors['email'] = 'Email tidak valid atau melebihi 100 karakter.';
        }
        if ($data['angkatan'] < 2000 || $data['angkatan'] > 2100) {
            $errors['angkatan'] = 'Angkatan harus antara 2000 dan 2100.';
        }
        if (!$this->prodiRepository->existsById($data['prodi_id'])) {
            $errors['prodi_id'] = 'Program studi wajib dipilih dan harus valid.';
        }

        return $errors;
    }

    /**
     * @return array{nim: string, nama: string, email: string, prodi_id: int, angkatan: int}
     */
    private function normalizeInput(array $input): array
    {
        return [
            'nim' => $this->stringValue($input['nim'] ?? null),
            'nama' => $this->stringValue($input['nama'] ?? null),
            'email' => $this->stringValue($input['email'] ?? null),
            'prodi_id' => $this->integerValue($input['prodi_id'] ?? null),
            'angkatan' => $this->integerValue($input['angkatan'] ?? null),
        ];
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function integerValue(mixed $value): int
    {
        if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
            return 0;
        }

        return (int) $value;
    }

    /** @param array<string, string> $errors */
    private function failure(array $errors, bool $saveFailed = true): array
    {
        $message = reset($errors) ?: 'Silakan coba kembali.';

        return [
            'success' => false,
            'message' => ($saveFailed ? 'Data gagal disimpan. ' : '') . $message,
            'errors' => $errors,
        ];
    }
}
