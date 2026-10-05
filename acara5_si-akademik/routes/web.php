<?php

return [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/mahasiswa' => ['MahasiswaController', 'index'],
        '/mahasiswa/create' => ['MahasiswaController', 'create'],
        '/mahasiswa/edit' => ['MahasiswaController', 'edit'],
    ],
    'POST' => [
        '/mahasiswa' => ['MahasiswaController', 'store'],
        '/mahasiswa/update' => ['MahasiswaController', 'update'],
    ],
];