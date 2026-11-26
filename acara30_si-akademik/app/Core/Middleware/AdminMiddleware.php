<?php

namespace App\Core\Middleware;

class AdminMiddleware extends AuthMiddleware
{
    public function handle(): void
    {
        parent::handle();

        if (($_SESSION['role'] ?? null) !== 'admin') {
            http_response_code(403);
            echo '403 - Akses ditolak, hanya admin yang diizinkan.';
            exit;
        }
    }
}
