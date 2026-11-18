<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$respond = static function (int $status, bool $success, string $message, mixed $data = null, array $extra = []): never {
    http_response_code($status);
    echo json_encode(
        ['success' => $success, 'message' => $message, 'data' => $data] + $extra,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
};

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, POST, PUT, DELETE');
    $respond(405, false, 'Metode HTTP tidak didukung.');
}

$rawId = $_GET['id'] ?? null;
if ($rawId === null && isset($_SERVER['PATH_INFO'])) {
    $rawId = trim((string) $_SERVER['PATH_INFO'], '/');
}
$id = null;
if ($rawId !== null) {
    if (!is_scalar($rawId) || filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
        $respond(400, false, 'ID mahasiswa harus berupa bilangan positif.');
    }
    $id = (int) $rawId;
}

if ($method === 'POST' && $id !== null) {
    header('Allow: GET, PUT, DELETE');
    $respond(405, false, 'POST hanya tersedia pada endpoint /api/mahasiswa.');
}
if (in_array($method, ['PUT', 'DELETE'], true) && $id === null) {
    header('Allow: GET, POST');
    $respond(405, false, "{$method} membutuhkan ID pada endpoint /api/mahasiswa/{id}.");
}

$parameters = null;
if (in_array($method, ['POST', 'PUT'], true)) {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (!preg_match('/^application\/json(?:\s*;|$)/i', $contentType)) {
        $respond(415, false, 'Content-Type harus application/json.');
    }

    try {
        $input = json_decode(file_get_contents('php://input'), flags: JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        $respond(400, false, 'Body harus berisi JSON yang valid.');
    }
    if (!is_object($input)) {
        $respond(400, false, 'Body JSON harus berupa object.');
    }

    $input = get_object_vars($input);
    $errors = [];
    foreach (['nim', 'nama', 'email', 'prodi_id', 'angkatan'] as $field) {
        if (!isset($input[$field]) || !is_scalar($input[$field]) || trim((string) $input[$field]) === '') {
            $errors[$field] = "Field {$field} wajib diisi.";
        }
    }
    if ($errors !== []) {
        $respond(422, false, 'Data mahasiswa belum lengkap.', null, ['errors' => $errors]);
    }

    $nim = trim((string) $input['nim']);
    $nama = trim((string) $input['nama']);
    $email = trim((string) $input['email']);
    $prodiId = filter_var($input['prodi_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $angkatan = filter_var($input['angkatan'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2000, 'max_range' => 2100]]);
    if (!ctype_digit($nim) || strlen($nim) > 20) {
        $errors['nim'] = 'NIM harus berupa angka dan maksimal 20 karakter.';
    }
    if (mb_strlen($nama) > 100) {
        $errors['nama'] = 'Nama maksimal 100 karakter.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errors['email'] = 'Email tidak valid atau melebihi 100 karakter.';
    }
    if ($prodiId === false) {
        $errors['prodi_id'] = 'Program studi harus berupa ID positif.';
    }
    if ($angkatan === false) {
        $errors['angkatan'] = 'Angkatan harus berupa tahun antara 2000 dan 2100.';
    }
    if ($errors !== []) {
        $respond(422, false, 'Data mahasiswa tidak valid.', null, ['errors' => $errors]);
    }

    $parameters = [
        'nim' => $nim,
        'nama' => $nama,
        'email' => $email,
        'prodi_id' => $prodiId,
        'angkatan' => $angkatan,
    ];
}

try {
    $config = require dirname(__DIR__) . '/app/config/database.php';
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
        if ($id !== null) {
            $statement = $pdo->prepare('SELECT id, nim, nama, email, prodi_id, angkatan FROM mahasiswa WHERE id = :id');
            $statement->execute(['id' => $id]);
            $mahasiswa = $statement->fetch();
            if ($mahasiswa === false) {
                $respond(404, false, 'Data mahasiswa tidak ditemukan.');
            }

            $respond(200, true, 'Data mahasiswa berhasil diambil.', $mahasiswa);
        }

        $statement = $pdo->query('SELECT id, nim, nama, email, prodi_id, angkatan FROM mahasiswa ORDER BY id');
        $respond(200, true, 'Data mahasiswa berhasil diambil.', $statement->fetchAll());
    }

    if ($method === 'DELETE') {
        $statement = $pdo->prepare('SELECT id, nim, nama, email, prodi_id, angkatan FROM mahasiswa WHERE id = :id');
        $statement->execute(['id' => $id]);
        $mahasiswa = $statement->fetch();
        if ($mahasiswa === false) {
            $respond(404, false, 'Data mahasiswa tidak ditemukan.');
        }

        $statement = $pdo->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $statement->execute(['id' => $id]);
        $respond(200, true, 'Data mahasiswa berhasil dihapus.', $mahasiswa);
    }

    if ($method === 'POST') {
        $statement = $pdo->prepare(
            'INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan)'
        );
        $statement->execute($parameters);
        $id = (int) $pdo->lastInsertId();
        $status = 201;
        $message = 'Data mahasiswa berhasil ditambahkan.';
    } else {
        $statement = $pdo->prepare('SELECT id FROM mahasiswa WHERE id = :id');
        $statement->execute(['id' => $id]);
        if ($statement->fetchColumn() === false) {
            $respond(404, false, 'Data mahasiswa tidak ditemukan.');
        }

        $statement = $pdo->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan
             WHERE id = :id'
        );
        $statement->execute($parameters + ['id' => $id]);
        $status = 200;
        $message = 'Data mahasiswa berhasil diperbarui.';
    }

    $statement = $pdo->prepare('SELECT id, nim, nama, email, prodi_id, angkatan FROM mahasiswa WHERE id = :id');
    $statement->execute(['id' => $id]);
    $mahasiswa = $statement->fetch();
    if ($mahasiswa === false) {
        $respond(500, false, 'Data tersimpan, tetapi gagal dibaca kembali.');
    }

    $respond($status, true, $message, $mahasiswa);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        $respond(422, false, 'NIM sudah terdaftar atau prodi_id tidak ditemukan.');
    }

    error_log('API mahasiswa gagal: ' . $exception->getMessage());
    $respond(500, false, 'Terjadi kesalahan saat mengakses database.');
}
