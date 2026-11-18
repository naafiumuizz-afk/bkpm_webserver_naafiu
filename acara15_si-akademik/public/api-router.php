<?php

declare(strict_types=1);

use App\Core\Database;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        global $baseUrl;

        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($baseUrl !== '' && ($requestPath === $baseUrl || str_starts_with($requestPath, $baseUrl . '/'))) {
    $requestPath = substr($requestPath, strlen($baseUrl)) ?: '/';
}
$route = '/' . trim($requestPath, '/');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$routes = require dirname(__DIR__) . '/routes/web.php';
$definition = $routes[$method][$route] ?? null;
$parameters = [];

if ($definition === null && $method === 'GET') {
    $protected = ['App\\Core\\Middleware\\AuthMiddleware'];
    if (preg_match('#^/mahasiswa/(\d+)/edit$#', $route, $matches)) {
        $definition = ['MahasiswaController', 'edit', $protected];
        $parameters = [$matches[1]];
    } elseif (preg_match('#^/mahasiswa/(\d+)$#', $route, $matches)) {
        $definition = ['MahasiswaController', 'show', $protected];
        $parameters = [$matches[1]];
    } elseif (preg_match('#^/prodi/(\d+)/edit$#', $route, $matches)) {
        $definition = ['ProdiController', 'edit', $protected];
        $parameters = [$matches[1]];
    } elseif (preg_match('#^/matakuliah/(\d+)/edit$#', $route, $matches)) {
        $definition = ['MatakuliahController', 'edit', $protected];
        $parameters = [$matches[1]];
    }
}

if ($definition === null) {
    http_response_code(404);
    echo '<h1>404</h1><p>Halaman tidak ditemukan.</p>';
    exit;
}

foreach ($definition[2] ?? [] as $middlewareClass) {
    (new $middlewareClass())->handle();
}

$database = new Database();
$mahasiswaRepository = new MahasiswaRepository($database);
$prodiRepository = new ProdiRepository($database);
$service = new MahasiswaService($mahasiswaRepository, $prodiRepository);
$controllerClass = 'App\\Controllers\\' . $definition[0];

$controller = match ($definition[0]) {
    'MahasiswaController' => new $controllerClass($mahasiswaRepository, $prodiRepository, $service),
    'MahasiswaApiController' => new $controllerClass($mahasiswaRepository, $service),
    default => new $controllerClass(),
};

$action = $definition[1];
$controller->{$action}(...$parameters);