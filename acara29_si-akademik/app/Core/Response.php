<?php

namespace App\Core;

class Response
{
    public static function json(
        int $httpCode,
        bool $success,
        string $message,
        mixed $data = null,
        array $errors = [],
        array $extra = []
    ): never {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');

        $config = require __DIR__ . '/../config/app.php';
        $flags = JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_INVALID_UTF8_SUBSTITUTE
            | JSON_THROW_ON_ERROR;
        if ($config['env'] === 'development') {
            $flags |= JSON_PRETTY_PRINT;
        }

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'timestamp' => date('c'),
        ] + $extra, $flags);
        exit;
    }
}
