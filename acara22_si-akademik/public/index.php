<?php

declare(strict_types=1);

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$publicDirectory = dirname($scriptName);
$projectDirectory = dirname($publicDirectory);
define('APP_BASE_PATH', $projectDirectory === '/' || $projectDirectory === '.'
    ? ''
    : rtrim($projectDirectory, '/'));

function app_url(string $path = ''): string
{
    return APP_BASE_PATH . '/' . ltrim($path, '/');
}

require_once dirname(__DIR__) . '/app/Core/autoload.php';

$router = new \App\Core\Router(require dirname(__DIR__) . '/routes/web.php');
$router->dispatch();
