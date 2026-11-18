<?php

declare(strict_types=1);

$baseUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
if ($baseUrl === '/') {
    $baseUrl = '';
} else {
    $baseUrl = rtrim($baseUrl, '/');
}

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        global $baseUrl;

        return $baseUrl . '/' . ltrim($path, '/');
    }
}

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

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($requestPath)) {
    http_response_code(400);
    echo '400 - URL tidak valid';
    exit;
}

if ($baseUrl !== '' && ($requestPath === $baseUrl || str_starts_with($requestPath, $baseUrl . '/'))) {
    $requestPath = substr($requestPath, strlen($baseUrl));
}
$requestPath = '/' . trim($requestPath, '/');
if ($requestPath === '/') {
    $requestPath = '/';
}

if (str_starts_with($requestPath, '/api/')) {
    require __DIR__ . '/api-router.php';
    exit;
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$routes = require dirname(__DIR__) . '/routes/web.php';
$route = $routes[$method][$requestPath] ?? null;
$parameters = [];

if ($route === null) {
    foreach ($routes[$method] ?? [] as $pattern => $definition) {
        $patternSegments = explode('/', trim($pattern, '/'));
        $pathSegments = explode('/', trim($requestPath, '/'));
        if (count($patternSegments) !== count($pathSegments)) {
            continue;
        }

        $candidateParameters = [];
        foreach ($patternSegments as $index => $segment) {
            if (preg_match('/^\{([A-Za-z][A-Za-z0-9_]*)\}$/', $segment, $matches) === 1) {
                $candidateParameters[] = rawurldecode($pathSegments[$index]);
            } elseif ($segment !== $pathSegments[$index]) {
                continue 2;
            }
        }

        $route = $definition;
        $parameters = $candidateParameters;
        break;
    }
}

if ($route === null) {
    $otherMethod = null;
    foreach ($routes as $supportedMethod => $methodRoutes) {
        if (isset($methodRoutes[$requestPath])) {
            $otherMethod = $supportedMethod;
            break;
        }
    }

    if ($otherMethod !== null) {
        header('Allow: ' . $otherMethod);
        http_response_code(405);
        echo '405 - Metode request tidak didukung';
        exit;
    }

    http_response_code(404);
    echo '404 - Halaman tidak ditemukan';
    exit;
}

[$controllerName, $action, $middleware] = array_pad($route, 3, []);
$controllerClass = 'App\\Controllers\\' . $controllerName;
$database = null;
$controller = null;

if (in_array($controllerName, ['MahasiswaController', 'MahasiswaApiController'], true)) {
    $database = new App\Core\Database();
    $mahasiswaRepository = new App\Repositories\MahasiswaRepository($database);
    $prodiRepository = new App\Repositories\ProdiRepository($database);
    $service = new App\Services\MahasiswaService($mahasiswaRepository, $prodiRepository);

    $controller = $controllerName === 'MahasiswaController'
        ? new $controllerClass($mahasiswaRepository, $prodiRepository, $service)
        : new $controllerClass($mahasiswaRepository, $service);
} else {
    $controller = new $controllerClass();
}

foreach ($middleware as $middlewareClass) {
    (new $middlewareClass())->handle();
}

$controller->{$action}(...$parameters);
