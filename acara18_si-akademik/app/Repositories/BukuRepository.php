<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Buku;
use PDO;

require_once __DIR__ . '/../Models/Buku.php';

class BukuRepository
{
    public function __construct(private Database $database)
    {
    }

    /** @return list<Buku> */
    public function all(): array
    {
        $rows = $this->connection()->query(
            'SELECT id, kode, judul, penulis, penerbit, tahun_terbit FROM buku ORDER BY id DESC'
        )->fetchAll();

        return array_map(fn (array $row): Buku => $this->fromRow($row), $rows);
    }

    public function find(int $id): ?Buku
    {
        $statement = $this->connection()->prepare(
            'SELECT id, kode, judul, penulis, penerbit, tahun_terbit FROM buku WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->fromRow($row);
    }

    public function existsByKode(string $kode, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM buku WHERE kode = :kode';
        $parameters = ['kode' => $kode];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public function create(Buku $buku): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO buku (kode, judul, penulis, penerbit, tahun_terbit)
             VALUES (:kode, :judul, :penulis, :penerbit, :tahun_terbit)'
        );
        $statement->execute($this->parameters($buku));

        return (int) $this->connection()->lastInsertId();
    }

    public function update(Buku $buku): void
    {
        $statement = $this->connection()->prepare(
            'UPDATE buku
             SET kode = :kode, judul = :judul, penulis = :penulis,
                 penerbit = :penerbit, tahun_terbit = :tahun_terbit
             WHERE id = :id'
        );
        $statement->execute($this->parameters($buku) + ['id' => $buku->id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM buku WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function connection(): PDO
    {
        return $this->database->getConnection();
    }

    /** @param array<string, mixed> $row */
    private function fromRow(array $row): Buku
    {
        return new Buku(
            $row['kode'],
            $row['judul'],
            $row['penulis'],
            $row['penerbit'],
            (int) $row['tahun_terbit'],
            (int) $row['id']
        );
    }

    /** @return array{kode: string, judul: string, penulis: string, penerbit: string, tahun_terbit: int} */
    private function parameters(Buku $buku): array
    {
        return [
            'kode' => $buku->kode,
            'judul' => $buku->judul,
            'penulis' => $buku->penulis,
            'penerbit' => $buku->penerbit,
            'tahun_terbit' => $buku->tahunTerbit,
        ];
    }
}
