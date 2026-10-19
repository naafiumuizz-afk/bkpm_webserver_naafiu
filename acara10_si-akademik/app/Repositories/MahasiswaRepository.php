<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Mahasiswa;
use PDO;

class MahasiswaRepository
{
    private ?bool $hasStatusColumn = null;
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /** @return list<Mahasiswa> */
    public function all(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $statement = $this->connection()->prepare(
            'SELECT ' . $this->selectColumns() . '
             FROM mahasiswa m
             INNER JOIN prodi p ON p.id = m.prodi_id
             WHERE m.nim LIKE :nim_search OR m.nama LIKE :nama_search
             ORDER BY m.id
             LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue('nim_search', '%' . $search . '%');
        $statement->bindValue('nama_search', '%' . $search . '%');
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            fn (array $row): Mahasiswa => $this->fromRow($row),
            $statement->fetchAll()
        );
    }

    public function count(string $search = ''): int
    {
        $statement = $this->connection()->prepare(
            'SELECT COUNT(*) FROM mahasiswa
             WHERE nim LIKE :nim_search OR nama LIKE :nama_search'
        );
        $statement->execute([
            'nim_search' => '%' . $search . '%',
            'nama_search' => '%' . $search . '%',
        ]);

        return (int) $statement->fetchColumn();
    }

    public function nimExists(string $nim, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM mahasiswa WHERE nim = :nim';
        $parameters = ['nim' => $nim];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public function prodiExists(int $id): bool
    {
        $statement = $this->connection()->prepare('SELECT 1 FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetchColumn() !== false;
    }

    public function find(int $id): ?Mahasiswa
    {
        $statement = $this->connection()->prepare(
            'SELECT ' . $this->selectColumns() . '
             FROM mahasiswa m
             INNER JOIN prodi p ON p.id = m.prodi_id
             WHERE m.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->fromRow($row);
    }

    public function create(Mahasiswa $mahasiswa): void
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan)'
        );
        $statement->execute($this->parameters($mahasiswa));
    }

    public function update(Mahasiswa $mahasiswa): void
    {
        $id = $mahasiswa->getId();
        if ($id === null) {
            throw new \InvalidArgumentException('ID mahasiswa wajib tersedia untuk proses pembaruan.');
        }

        $statement = $this->connection()->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan
             WHERE id = :id'
        );
        $statement->execute($this->parameters($mahasiswa) + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /** @return list<array{id: int, kode: string, nama: string}> */
    public function programStudi(): array
    {
        return $this->connection()->query('SELECT id, kode, nama FROM prodi ORDER BY nama')->fetchAll();
    }

    private function selectColumns(): string
    {
        if ($this->hasStatusColumn === null) {
            $columns = $this->connection()->query("SHOW COLUMNS FROM mahasiswa LIKE 'status'")->fetch();
            $this->hasStatusColumn = $columns !== false;
        }

        $statusColumn = $this->hasStatusColumn ? 'm.status' : "'aktif' AS status";

        return "m.id, m.nim, m.nama, m.email, m.prodi_id, m.angkatan, {$statusColumn}, p.nama AS prodi";
    }

    private function connection(): PDO
    {
        return $this->database->getConnection();
    }

    private function fromRow(array $row): Mahasiswa
    {
        return new Mahasiswa(
            $row['nim'],
            $row['nama'],
            $row['prodi'],
            $row['email'],
            (int) $row['prodi_id'],
            (int) $row['angkatan'],
            $row['status'],
            (int) $row['id']
        );
    }

    /** @return array{nim: string, nama: string, email: string, prodi_id: int, angkatan: int} */
    private function parameters(Mahasiswa $mahasiswa): array
    {
        return [
            'nim' => $mahasiswa->getNim(),
            'nama' => $mahasiswa->getNama(),
            'email' => $mahasiswa->getEmail(),
            'prodi_id' => $mahasiswa->getProdiId(),
            'angkatan' => (int) $mahasiswa->getAngkatan(),
        ];
    }
}
