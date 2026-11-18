<?php

declare(strict_types=1);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($requestPath)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Path request tidak valid.', 'data' => null]);
    exit;
}

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/api-router.php'));
if ($basePath !== '/') {
    $basePath = rtrim($basePath, '/');
    if ($basePath !== '' && ($requestPath === $basePath || str_starts_with($requestPath, $basePath . '/'))) {
        $requestPath = substr($requestPath, strlen($basePath));
    }
}

$route = trim($requestPath, '/');
if ($route === '' || $route === 'api/mahasiswa') {
    unset($_SERVER['PATH_INFO']);
    require dirname(__DIR__) . '/api/mahasiswa.php';
    exit;
}

if (preg_match('#^api/mahasiswa/([^/]+)$#', $route, $matches) === 1) {
    $_SERVER['PATH_INFO'] = '/' . rawurldecode($matches[1]);
    require dirname(__DIR__) . '/api/mahasiswa.php';
    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(
    ['success' => false, 'message' => 'Endpoint tidak ditemukan.', 'data' => null],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
