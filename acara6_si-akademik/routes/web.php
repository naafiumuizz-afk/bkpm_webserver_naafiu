<?php

return [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/login' => ['AuthController', 'loginForm'],
        '/logout' => ['AuthController', 'logout'],
        '/dashboard' => ['HomeController', 'dashboard', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa' => ['MahasiswaController', 'index', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/create' => ['MahasiswaController', 'create', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/edit' => ['MahasiswaController', 'edit', ['App\\Core\\Middleware\\AuthMiddleware']],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/mahasiswa' => ['MahasiswaController', 'store', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/update' => ['MahasiswaController', 'update', ['App\\Core\\Middleware\\AuthMiddleware']],
    ],
];