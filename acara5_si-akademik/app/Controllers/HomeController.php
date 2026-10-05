<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        echo '<h1>Selamat Datang di Sistem Akademik</h1>';
        echo '<p><a href="mahasiswa">Daftar Mahasiswa</a></p>';
    }
}