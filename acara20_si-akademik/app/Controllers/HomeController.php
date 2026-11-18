<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $path = !empty($_SESSION['logged_in']) ? '/dashboard' : '/login';
        header('Location: ' . app_url($path));
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
}