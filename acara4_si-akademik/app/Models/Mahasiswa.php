<?php

namespace App\Models;

class Mahasiswa
{
    private string $nim;
    private string $nama;
    private string $prodi;

    public function __construct(string $nim, string $nama, string $prodi)
    {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->prodi = $prodi;
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getProdi(): string
    {
        return $this->prodi;
    }

    public function setNama(string $nama): void
    {
        if (strlen(trim($nama)) < 3) {
            throw new \InvalidArgumentException('Nama terlalu pendek');
        }

        $this->nama = $nama;
    }

    public function getAngkatan(): string
    {
        if (strlen($this->nim) < 2) {
            return 'N/A';
        }

        $angkatan = substr($this->nim, 0, 2);

        if (!is_numeric($angkatan)) {
            return 'N/A';
        }

        return '20' . $angkatan;
    }
}
