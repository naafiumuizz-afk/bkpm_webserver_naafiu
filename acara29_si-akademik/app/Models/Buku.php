<?php

namespace App\Models;

class Buku
{
    public function __construct(
        public readonly string $kode,
        public readonly string $judul,
        public readonly string $penulis,
        public readonly string $penerbit,
        public readonly int $tahunTerbit,
        public readonly ?int $id = null
    ) {
        if ($kode === '' || mb_strlen($kode) > 20) {
            throw new \InvalidArgumentException('Kode buku wajib diisi dan maksimal 20 karakter.');
        }
        if ($judul === '' || mb_strlen($judul) > 150) {
            throw new \InvalidArgumentException('Judul buku wajib diisi dan maksimal 150 karakter.');
        }
        if ($penulis === '' || mb_strlen($penulis) > 100) {
            throw new \InvalidArgumentException('Nama penulis wajib diisi dan maksimal 100 karakter.');
        }
        if ($penerbit === '' || mb_strlen($penerbit) > 100) {
            throw new \InvalidArgumentException('Nama penerbit wajib diisi dan maksimal 100 karakter.');
        }
        if ($tahunTerbit < 1000 || $tahunTerbit > 2100) {
            throw new \InvalidArgumentException('Tahun terbit harus antara 1000 dan 2100.');
        }
        if ($id !== null && $id < 1) {
            throw new \InvalidArgumentException('ID buku harus berupa bilangan positif.');
        }
    }

    /** @return array{id: int|null, kode: string, judul: string, penulis: string, penerbit: string, tahun_terbit: int} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'judul' => $this->judul,
            'penulis' => $this->penulis,
            'penerbit' => $this->penerbit,
            'tahun_terbit' => $this->tahunTerbit,
        ];
    }
}
