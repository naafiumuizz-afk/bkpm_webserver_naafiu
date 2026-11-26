<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $path = !empty($_SESSION['logged_in']) ? '/dashboard' : '/login';
        header('Location: ' . app_url($path));
        exit;
    }

    public function dashboard(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pageTitle = 'Dashboard | SI Akademik';
        $userName = $_SESSION['user_name'] ?? '';
        $userRole = $_SESSION['role'] ?? '';
        ob_start();
        require __DIR__ . '/../../public/views/dashboard.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../public/views/layouts/main.php';
    }
}