<?php

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class Mahasiswa extends BaseModel
{
    private ?int $id;
    private string $nim;
    private string $nama;
    private string $email;
    private string $prodi;
    private int $prodiId;
    private int $angkatan;
    private string $status;

    public function __construct(
        string $nim,
        string $nama,
        string $prodi,
        string $email = '',
        int $prodiId = 0,
        int $angkatan = 0,
        string $status = 'aktif',
        ?int $id = null
    )
    {
        $this->setId($id);
        $this->setNim($nim);
        $this->setNama($nama);
        $this->setEmail($email);
        $this->setProdi($prodi);
        $this->setProdiId($prodiId);
        $this->setAngkatan($angkatan);
        $this->setStatus($status);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        if ($id !== null && $id < 1) {
            throw new \InvalidArgumentException('ID mahasiswa harus berupa bilangan positif.');
        }
        $this->id = $id;
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function setNim(string $nim): void
    {
        $nim = trim($nim);
        if ($nim === '' || !ctype_digit($nim) || strlen($nim) > 20) {
            throw new \InvalidArgumentException('NIM wajib diisi, harus berupa angka, dan maksimal 20 karakter.');
        }
        $this->nim = $nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function setNama(string $nama): void
    {
        $nama = trim($nama);
        if ($nama === '' || mb_strlen($nama) > 100) {
            throw new \InvalidArgumentException('Nama mahasiswa wajib diisi dan maksimal 100 karakter.');
        }
        $this->nama = $nama;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = trim($email);
    }

    public function getProdi(): string
    {
        return $this->prodi;
    }

    public function setProdi(string $prodi): void
    {
        $this->prodi = trim($prodi);
    }

    public function getProdiId(): int
    {
        return $this->prodiId;
    }

    public function setProdiId(int $prodiId): void
    {
        if ($prodiId < 0) {
            throw new \InvalidArgumentException('ID program studi tidak valid.');
        }
        $this->prodiId = $prodiId;
    }

    public function getAngkatan(): string
    {
        return $this->angkatan > 0 ? (string) $this->angkatan : '-';
    }

    public function setAngkatan(int $angkatan): void
    {
        if ($angkatan < 0) {
            throw new \InvalidArgumentException('Angkatan tidak valid.');
        }
        $this->angkatan = $angkatan;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, ['aktif', 'cuti', 'lulus'], true)) {
            throw new \InvalidArgumentException('Status mahasiswa tidak valid.');
        }
        $this->status = $status;
    }
}