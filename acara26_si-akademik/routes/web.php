<?php

return [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/api/mahasiswa' => ['MahasiswaApiController', 'index'],
        '/api/mahasiswa/{id}' => ['MahasiswaApiController', 'show'],
        '/api/buku' => ['BukuApiController', 'index'],
        '/api/buku/{id}' => ['BukuApiController', 'show'],
        '/login' => ['AuthController', 'loginForm'],
        '/logout' => ['AuthController', 'logout'],
        '/dashboard' => ['HomeController', 'dashboard', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa' => ['MahasiswaController', 'index', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/create' => ['MahasiswaController', 'create', ['App\\Core\\Middleware\\AdminMiddleware']],
        '/mahasiswa/edit' => ['MahasiswaController', 'edit', ['App\\Core\\Middleware\\AdminMiddleware']],
        '/mahasiswa/{id}' => ['MahasiswaController', 'show', ['App\\Core\\Middleware\\AuthMiddleware']],
    ],
    'POST' => [
        '/api/login' => ['AuthController', 'apiLogin'],
        '/api/mahasiswa' => ['MahasiswaApiController', 'store'],
        '/api/buku' => ['BukuApiController', 'store'],
        '/register' => ['AuthController', 'register'],
        '/login' => ['AuthController', 'login'],
        '/mahasiswa' => ['MahasiswaController', 'store', ['App\\Core\\Middleware\\AdminMiddleware']],
        '/mahasiswa/update' => ['MahasiswaController', 'update', ['App\\Core\\Middleware\\AdminMiddleware']],
        '/mahasiswa/delete' => ['MahasiswaController', 'destroy', ['App\\Core\\Middleware\\AdminMiddleware']],
        '/mahasiswa/destroy' => ['MahasiswaController', 'destroy', ['App\\Core\\Middleware\\AdminMiddleware']],
    ],
    'PUT' => [
        '/api/mahasiswa/{id}' => ['MahasiswaApiController', 'update'],
        '/api/buku/{id}' => ['BukuApiController', 'update'],
    ],
    'DELETE' => [
        '/api/mahasiswa/{id}' => ['MahasiswaApiController', 'destroy'],
        '/api/buku/{id}' => ['BukuApiController', 'destroy'],
    ],
];