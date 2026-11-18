<?php

namespace App\Core;

use App\Controllers\BukuApiController;
use App\Controllers\AuthController;
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
                    if (!is_string($middlewareClass) || !class_exists($middlewareClass)) {
                        $this->respond(500, 'Middleware route tidak tersedia.');

                        return;
                    }

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
        $controller = match ($controllerName) {
            'HomeController' => new HomeController(),
            'AuthController' => new AuthController(),
            'MahasiswaController' => $this->createMahasiswaController(),
            'ProdiController' => new ProdiController(),
            'MatakuliahController' => new MatakuliahController(),
            'MahasiswaApiController' => $this->createMahasiswaApiController(),
            'BukuApiController' => new BukuApiController(new BukuRepository(new Database())),
            default => null,
        };

        if ($controller === null) {
            $this->respond(404, 'Endpoint tidak ditemukan.');
            return;
        }

        if ($id === null) {
            $controller->{$action}();

            return;
        }

        $controller->{$action}((string) $id);
    }

    private function createMahasiswaController(): MahasiswaController
    {
        $database = new Database();
        $mahasiswaRepository = new MahasiswaRepository($database);
        $prodiRepository = new ProdiRepository($database);

        return new MahasiswaController(
            $mahasiswaRepository,
            $prodiRepository,
            new MahasiswaService($mahasiswaRepository, $prodiRepository)
        );
    }

    private function createMahasiswaApiController(): MahasiswaApiController
    {
        $database = new Database();
        $repository = new MahasiswaRepository($database);

        return new MahasiswaApiController(
            $repository,
            new MahasiswaService($repository, new ProdiRepository($database))
        );
    }

    private function respond(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            ['success' => false, 'message' => $message, 'data' => null],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
