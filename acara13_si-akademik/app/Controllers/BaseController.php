<?php

namespace App\Controllers;

abstract class BaseController
{
    protected function view(string $view, string $pageTitle, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
