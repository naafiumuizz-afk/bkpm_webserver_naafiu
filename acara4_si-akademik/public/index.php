<?php
require_once __DIR__ . '/../app/Models/Mahasiswa.php';

$baseUrl = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (!is_string($baseUrl) || $baseUrl === '') {
    $baseUrl = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
}
if (substr($baseUrl, -4) !== '.php') {
    $baseUrl = rtrim($baseUrl, '/') . '/index.php';
}

$pageTitle = 'Daftar Mahasiswa';
$mahasiswaList = [
    new App\Models\Mahasiswa('2301001', 'Andi Pratama', 'Teknik Informatika'),
    new App\Models\Mahasiswa('2301002', 'Siti Rahma', 'Sistem Informasi'),
    new App\Models\Mahasiswa('2301003', 'Rizky Maulana', 'Manajemen Informatika'),
];

require __DIR__ . '/../app/Views/Mahasiswa/index.php';
