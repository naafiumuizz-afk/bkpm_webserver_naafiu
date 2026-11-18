<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $this->apiFlow();
    }

    public function apiFlow(): void
    {
        $pageTitle = 'Alur API CRUD Buku | SI Akademik';
        $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($baseUrl === '.' || $baseUrl === '/') {
            $baseUrl = '';
        }

        ob_start();
        require __DIR__ . '/../../public/views/api-flow.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../public/views/layouts/main.php';
    }

    public function dashboard(): void
    {
        $pageTitle = 'Dashboard | SI Akademik';
        ob_start();
        require __DIR__ . '/../../public/views/dashboard.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../public/views/layouts/main.php';
    }
}