<?php

namespace App\Core\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Anda harus login terlebih dahulu.',
            ];
            header('Location: ' . app_url('/login'));
            exit;
        }
    }
}
