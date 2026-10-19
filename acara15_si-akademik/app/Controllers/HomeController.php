<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        echo '<h1>Selamat Datang di Sistem Akademik</h1>';
        echo '<p><a href="' . app_url('/dashboard') . '">Masuk ke dashboard</a></p>';
    }

    public function dashboard(): void
    {
        $pageTitle = 'Dashboard | SI Akademik';
        ob_start();
        require __DIR__ . '/../Views/dashboard.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }
}