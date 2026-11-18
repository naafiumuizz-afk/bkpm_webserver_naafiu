<?php

require_once __DIR__ . '/../app/Core/autoload.php';

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($basePath === '/' || $basePath === '.') {
            $basePath = '';
        }

        return rtrim($basePath, '/') . '/' . ltrim($path, '/');
    }
}

$routes = require __DIR__ . '/../routes/web.php';
(new App\Core\Router($routes))->dispatch();
