<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$respond = static function (int $status, bool $success, string $message, mixed $data): never {
    http_response_code($status);
    echo json_encode(
        ['success' => $success, 'message' => $message, 'data' => $data],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
};

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    $respond(405, false, 'Metode HTTP tidak didukung.', null);
}

try {
    $config = require dirname(__DIR__) . '/config/database.php';
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['dbname'],
        $config['charset']
    );
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false) {
                $respond(400, false, 'ID mahasiswa harus berupa bilangan positif.', null);
            }

            $statement = $pdo->prepare('SELECT id, nim, nama, email FROM mahasiswa WHERE id = :id');
            $statement->execute(['id' => $id]);
            $mahasiswa = $statement->fetch();
            if ($mahasiswa === false) {
                $respond(404, false, 'Data mahasiswa tidak ditemukan.', null);
            }

            $respond(200, true, 'Data mahasiswa berhasil diambil.', $mahasiswa);
        }

        $statement = $pdo->query('SELECT id, nim, nama, email FROM mahasiswa ORDER BY id');
        $respond(200, true, 'Data mahasiswa berhasil diambil.', $statement->fetchAll());
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (!preg_match('/^application\/json(?:\s*;|$)/i', $contentType)) {
        $respond(415, false, 'Content-Type harus application/json.', null);
    }

    try {
        $input = json_decode(file_get_contents('php://input'), flags: JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        $respond(400, false, 'Body harus berisi JSON yang valid.', null);
    }
    if (!is_object($input)) {
        $respond(400, false, 'Body JSON harus berupa object.', null);
    }

    $input = get_object_vars($input);
    foreach (['nim', 'nama', 'email', 'prodi_id', 'angkatan'] as $field) {
        if (!isset($input[$field]) || !is_scalar($input[$field]) || trim((string) $input[$field]) === '') {
            $respond(422, false, "Field {$field} wajib diisi.", null);
        }
    }

    $nim = trim((string) $input['nim']);
    $nama = trim((string) $input['nama']);
    $email = trim((string) $input['email']);
    $prodiId = filter_var($input['prodi_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $angkatan = filter_var($input['angkatan'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2000, 'max_range' => 2100]]);
    if (!ctype_digit($nim) || strlen($nim) > 20 || strlen($nama) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100 || $prodiId === false || $angkatan === false) {
        $respond(422, false, 'Data mahasiswa tidak valid.', null);
    }

    $statement = $pdo->prepare(
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

    $statement = $pdo->prepare('SELECT id, nim, nama, email FROM mahasiswa WHERE id = :id');
    $statement->execute(['id' => $pdo->lastInsertId()]);
    $respond(201, true, 'Data mahasiswa berhasil ditambahkan.', $statement->fetch());
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        $respond(422, false, 'NIM sudah terdaftar atau prodi_id tidak ditemukan.', null);
    }

    error_log('API mahasiswa gagal: ' . $exception->getMessage());
    $respond(500, false, 'Terjadi kesalahan saat mengakses database.', null);
}