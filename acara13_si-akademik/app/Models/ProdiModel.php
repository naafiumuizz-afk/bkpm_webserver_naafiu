<?php

namespace App\Models;

require_once __DIR__ . '/Model.php';

class ProdiModel extends Model
{
    public static function all(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $statement = static::connection()->prepare(
            'SELECT id, kode, nama FROM prodi
             WHERE kode LIKE :kode_search OR nama LIKE :nama_search
             ORDER BY nama LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue('kode_search', '%' . $search . '%');
        $statement->bindValue('nama_search', '%' . $search . '%');
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function count(string $search = ''): int
    {
        $statement = static::connection()->prepare(
            'SELECT COUNT(*) FROM prodi WHERE kode LIKE :kode_search OR nama LIKE :nama_search'
        );
        $statement->execute([
            'kode_search' => '%' . $search . '%',
            'nama_search' => '%' . $search . '%',
        ]);

        return (int) $statement->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $statement = static::connection()->prepare('SELECT id, kode, nama FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    public static function nameExists(string $nama, ?int $exceptId = null): bool
    {
        return static::exists('nama', $nama, $exceptId);
    }

    public static function codeExists(string $kode, ?int $exceptId = null): bool
    {
        return static::exists('kode', $kode, $exceptId);
    }

    private static function exists(string $column, string $value, ?int $exceptId): bool
    {
        $sql = "SELECT 1 FROM prodi WHERE {$column} = :value";
        $parameters = ['value' => $value];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }
        $statement = static::connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public static function create(string $kode, string $nama): void
    {
        $statement = static::connection()->prepare('INSERT INTO prodi (kode, nama) VALUES (:kode, :nama)');
        $statement->execute(['kode' => $kode, 'nama' => $nama]);
    }

    public static function update(int $id, string $kode, string $nama): void
    {
        $statement = static::connection()->prepare('UPDATE prodi SET kode = :kode, nama = :nama WHERE id = :id');
        $statement->execute(['id' => $id, 'kode' => $kode, 'nama' => $nama]);
    }

    public static function delete(int $id): void
    {
        $statement = static::connection()->prepare('DELETE FROM prodi WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
