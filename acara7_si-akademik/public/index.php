<?php

require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Core/Middleware/AuthMiddleware.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');

if (!function_exists('app_url')) {
    function app_url(string $path): string
    {
        global $basePath;

        return $basePath . '/' . ltrim($path, '/');
    }
}

if ($basePath !== '' && $basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}

$uri = '/' . trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$routes = require __DIR__ . '/../routes/web.php';
$route = $routes[$method][$uri] ?? null;
$parameters = [];

if ($route === null && $method === 'GET' && preg_match('#^/mahasiswa/([1-9]\d*)$#', $uri, $matches)) {
    $route = ['MahasiswaController', 'show', ['App\\Core\\Middleware\\AuthMiddleware']];
    $parameters = [$matches[1]];
}

if ($route === null) {
    http_response_code(404);
    echo '<h1>404 - Halaman tidak ditemukan</h1>';
    exit;
}

[$controllerName, $action, $middleware] = array_pad($route, 3, []);

foreach ($middleware as $middlewareClass) {
    (new $middlewareClass())->handle();
}

$controllerClass = 'App\\Controllers\\' . $controllerName;
$controller = new $controllerClass();
$controller->$action(...$parameters);
