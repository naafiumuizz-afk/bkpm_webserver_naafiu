<?php

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

class DosenController extends BaseController
{
    public function index(): void
    {
        $this->view('Dosen/index', 'Data Dosen | SI Akademik');
    }
}
