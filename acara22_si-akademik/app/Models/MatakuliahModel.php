<?php

namespace App\Models;

require_once __DIR__ . '/Model.php';

class MatakuliahModel extends Model
{
    public static function all(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $statement = static::connection()->prepare(
            'SELECT m.id, m.kode, m.nama, m.sks, m.prodi_id, p.nama AS prodi_nama
             FROM matakuliah m
             INNER JOIN prodi p ON p.id = m.prodi_id
             WHERE m.kode LIKE :kode_search OR m.nama LIKE :nama_search OR p.nama LIKE :prodi_search
             ORDER BY m.nama LIMIT :limit OFFSET :offset'
        );
        $pattern = '%' . $search . '%';
        $statement->bindValue('kode_search', $pattern);
        $statement->bindValue('nama_search', $pattern);
        $statement->bindValue('prodi_search', $pattern);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function count(string $search = ''): int
    {
        $statement = static::connection()->prepare(
            'SELECT COUNT(*) FROM matakuliah m
             INNER JOIN prodi p ON p.id = m.prodi_id
             WHERE m.kode LIKE :kode_search OR m.nama LIKE :nama_search OR p.nama LIKE :prodi_search'
        );
        $pattern = '%' . $search . '%';
        $statement->execute([
            'kode_search' => $pattern,
            'nama_search' => $pattern,
            'prodi_search' => $pattern,
        ]);

        return (int) $statement->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $statement = static::connection()->prepare(
            'SELECT id, kode, nama, sks, prodi_id FROM matakuliah WHERE id = :id'
        );
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
        $sql = "SELECT 1 FROM matakuliah WHERE {$column} = :value";
        $parameters = ['value' => $value];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }
        $statement = static::connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public static function create(string $kode, string $nama, int $sks, int $prodiId): void
    {
        $statement = static::connection()->prepare(
            'INSERT INTO matakuliah (kode, nama, sks, prodi_id)
             VALUES (:kode, :nama, :sks, :prodi_id)'
        );
        $statement->execute(['kode' => $kode, 'nama' => $nama, 'sks' => $sks, 'prodi_id' => $prodiId]);
    }

    public static function update(int $id, string $kode, string $nama, int $sks, int $prodiId): void
    {
        $statement = static::connection()->prepare(
            'UPDATE matakuliah SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'kode' => $kode,
            'nama' => $nama,
            'sks' => $sks,
            'prodi_id' => $prodiId,
        ]);
    }

    public static function delete(int $id): void
    {
        $statement = static::connection()->prepare('DELETE FROM matakuliah WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
