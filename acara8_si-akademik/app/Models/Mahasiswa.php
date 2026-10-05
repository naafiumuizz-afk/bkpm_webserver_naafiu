<?php

namespace App\Models;

require_once __DIR__ . '/Model.php';

class Mahasiswa extends Model
{
    private static ?bool $hasStatusColumn = null;
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
        $this->id = $id;
        $this->nim = strtoupper(trim($nim));
        $this->nama = trim($nama);
        $this->email = trim($email);
        $this->prodi = trim($prodi);
        $this->prodiId = $prodiId;
        $this->angkatan = $angkatan;
        $this->status = $status;
    }

    /** @return list<static> */
    public static function all(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $statement = static::connection()->prepare(
            'SELECT ' . static::selectColumns() . '
             FROM mahasiswa m
             INNER JOIN prodi p ON p.id = m.prodi_id
               WHERE m.nim LIKE :nim_search OR m.nama LIKE :nama_search
             ORDER BY m.id'
             . ' LIMIT :limit OFFSET :offset'
        );
           $statement->bindValue('nim_search', '%' . $search . '%');
           $statement->bindValue('nama_search', '%' . $search . '%');
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->execute();

        return array_map(fn (array $row): static => static::fromRow($row), $statement->fetchAll());
    }

    public static function count(string $search = ''): int
    {
        $statement = static::connection()->prepare(
            'SELECT COUNT(*) FROM mahasiswa
               WHERE nim LIKE :nim_search OR nama LIKE :nama_search'
        );
           $statement->execute([
              'nim_search' => '%' . $search . '%',
              'nama_search' => '%' . $search . '%',
           ]);

        return (int) $statement->fetchColumn();
    }

    public static function nimExists(string $nim, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM mahasiswa WHERE nim = :nim';
        $parameters = ['nim' => $nim];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = static::connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public static function find(int $id): ?static
    {
        $statement = static::connection()->prepare(
            'SELECT ' . static::selectColumns() . '
             FROM mahasiswa m
             INNER JOIN prodi p ON p.id = m.prodi_id
             WHERE m.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : static::fromRow($row);
    }

    private static function selectColumns(): string
    {
        if (self::$hasStatusColumn === null) {
            $columns = static::connection()->query('SHOW COLUMNS FROM mahasiswa LIKE \'status\'')->fetch();
            self::$hasStatusColumn = $columns !== false;
        }

        $statusColumn = self::$hasStatusColumn ? 'm.status' : "'aktif' AS status";

        return "m.id, m.nim, m.nama, m.email, m.prodi_id, m.angkatan, {$statusColumn}, p.nama AS prodi";
    }

    public static function create(string $nim, string $nama, string $email, int $prodiId, int $angkatan): void
    {
        $statement = static::connection()->prepare(
            'INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan)'
        );
        $statement->execute([
            'nim' => $nim,
            'nama' => $nama,
            'email' => $email,
            'prodi_id' => $prodiId,
            'angkatan' => $angkatan,
        ]);
    }

    public static function update(int $id, string $nim, string $nama, string $email, int $prodiId, int $angkatan): void
    {
        $statement = static::connection()->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'nim' => $nim,
            'nama' => $nama,
            'email' => $email,
            'prodi_id' => $prodiId,
            'angkatan' => $angkatan,
        ]);
    }

    public static function delete(int $id): void
    {
        $statement = static::connection()->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private static function fromRow(array $row): static
    {
        return new static(
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getProdiId(): int
    {
        return $this->prodiId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAngkatan(): string
    {
        return $this->angkatan > 0 ? (string) $this->angkatan : '-';
    }
}