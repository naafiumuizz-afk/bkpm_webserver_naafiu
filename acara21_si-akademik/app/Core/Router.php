<?php

namespace App\Core;

use App\Controllers\BukuApiController;
use App\Controllers\AuthController;
use App\Controllers\DosenController;
use App\Controllers\HomeController;
use App\Controllers\MahasiswaController;
use App\Controllers\MahasiswaApiController;
use App\Controllers\MatakuliahController;
use App\Controllers\ProdiController;
use App\Repositories\BukuRepository;
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../Repositories/BukuRepository.php';
require_once __DIR__ . '/../Controllers/BukuApiController.php';
require_once __DIR__ . '/../Controllers/MahasiswaApiController.php';

class Router
{
    public function __construct(private array $routes)
    {
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (!is_string($path)) {
            $this->respond(400, 'URL tidak valid.');

            return;
        }

        $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDirectory !== '/' && $scriptDirectory !== '.' && str_starts_with($path, $scriptDirectory . '/')) {
            $path = substr($path, strlen($scriptDirectory));
        } elseif (defined('APP_BASE_PATH') && APP_BASE_PATH !== ''
            && ($path === APP_BASE_PATH || str_starts_with($path, APP_BASE_PATH . '/'))) {
            $path = substr($path, strlen(APP_BASE_PATH));
        }
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        $allowedMethods = [];
        foreach ($this->routes as $routeMethod => $definitions) {
            foreach ($definitions as $route => $definition) {
                $pattern = str_replace('\{id\}', '([1-9][0-9]*)', preg_quote($route, '#'));
                if (!preg_match('#^' . $pattern . '$#', $path, $matches)) {
                    continue;
                }

                $allowedMethods[] = $routeMethod;
                if ($routeMethod !== $method) {
                    continue;
                }

                $id = isset($matches[1])
                    ? filter_var($matches[1], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                    : null;
                if (isset($matches[1]) && $id === false) {
                    $this->respond(400, 'ID harus berupa bilangan positif.');

                    return;
                }

                foreach ($definition[2] ?? [] as $middlewareClass) {
                    (new $middlewareClass())->handle();
                }

                $this->invoke($definition[0], $definition[1], $id === false ? null : $id);

                return;
            }
        }

        if ($allowedMethods !== []) {
            header('Allow: ' . implode(', ', array_unique($allowedMethods)));
            $this->respond(405, 'Method tidak diizinkan.');

            return;
        }

        $this->respond(404, 'Endpoint tidak ditemukan.');
    }

    private function invoke(string $controllerName, string $action, ?int $id): void
    {
        $database = new Database();
        $repository = null;
        $service = null;

        if ($controllerName === 'MahasiswaApiController') {
            $repository = new MahasiswaRepository($database);
            $service = new MahasiswaService($repository, new ProdiRepository($database));
            $controller = new MahasiswaApiController($repository, $service);
        } elseif ($controllerName === 'MahasiswaController') {
            $repository = new MahasiswaRepository($database);
            $service = new MahasiswaService($repository, new ProdiRepository($database));
            $controller = new MahasiswaController($repository, new ProdiRepository($database), $service);
        } elseif ($controllerName === 'BukuApiController') {
            $controller = new BukuApiController(new BukuRepository($database));
        } elseif ($controllerName === 'AuthController') {
            $controller = new AuthController();
        } elseif ($controllerName === 'HomeController') {
            $controller = new HomeController();
        } elseif ($controllerName === 'ProdiController') {
            $controller = new ProdiController();
        } elseif ($controllerName === 'MatakuliahController') {
            $controller = new MatakuliahController();
        } elseif ($controllerName === 'DosenController') {
            $controller = new DosenController();
        } else {
            $this->respond(404, 'Halaman tidak ditemukan.');

            return;
        }

        if ($id === null) {
            $controller->{$action}();

            return;
        }

        $controller->{$action}($id);
    }

    private function respond(int $status, string $message): never
    {
        Response::json($status, false, $message);
    }
}
