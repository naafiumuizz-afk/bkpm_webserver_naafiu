<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $destination = !empty($_SESSION['logged_in']) ? '/dashboard' : '/login';
        header('Location: ' . app_url($destination));
        exit;
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