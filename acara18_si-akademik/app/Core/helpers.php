<?php

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $baseUrl = rtrim(dirname($scriptName), '/');
        if ($baseUrl === '.' || $baseUrl === '/') {
            $baseUrl = '';
        }

        return $baseUrl . '/' . ltrim($path, '/');
    }
}
