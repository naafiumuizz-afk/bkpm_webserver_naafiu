<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProdiRepository
{
    public function __construct(private Database $database)
    {
    }

    public function existsById(int $id): bool
    {
        $statement = $this->connection()->prepare('SELECT 1 FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetchColumn() !== false;
    }

    /** @return list<array{id: int, kode: string, nama: string}> */
    public function all(): array
    {
        return $this->connection()->query('SELECT id, kode, nama FROM prodi ORDER BY nama')->fetchAll();
    }

    private function connection(): PDO
    {
        return $this->database->getConnection();
    }
}
