<?php

return [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/login' => ['AuthController', 'loginForm'],
        '/logout' => ['AuthController', 'logout'],
        '/dashboard' => ['HomeController', 'dashboard', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa' => ['MahasiswaController', 'index', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/create' => ['MahasiswaController', 'create', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/prodi' => ['ProdiController', 'index', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/prodi/create' => ['ProdiController', 'create', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/matakuliah' => ['MatakuliahController', 'index', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/matakuliah/create' => ['MatakuliahController', 'create', ['App\\Core\\Middleware\\AuthMiddleware']],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/mahasiswa' => ['MahasiswaController', 'store', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/update' => ['MahasiswaController', 'update', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/mahasiswa/destroy' => ['MahasiswaController', 'destroy', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/prodi' => ['ProdiController', 'store', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/prodi/update' => ['ProdiController', 'update', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/prodi/destroy' => ['ProdiController', 'destroy', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/matakuliah' => ['MatakuliahController', 'store', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/matakuliah/update' => ['MatakuliahController', 'update', ['App\\Core\\Middleware\\AuthMiddleware']],
        '/matakuliah/destroy' => ['MatakuliahController', 'destroy', ['App\\Core\\Middleware\\AuthMiddleware']],
    ],
];