<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        header('Location: ' . app_url('/dashboard'));
        exit;
    }

    public function dashboard(): void
    {
        $pageTitle = 'Dashboard | SI Akademik';
        ob_start();
        require __DIR__ . '/../../public/views/dashboard.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../public/views/layouts/main.php';
    }

    public function dosen(): void
    {
        $pageTitle = 'Dosen | SI Akademik';
        ob_start();
        require __DIR__ . '/../../public/views/Dosen/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../public/views/layouts/main.php';
    }
}