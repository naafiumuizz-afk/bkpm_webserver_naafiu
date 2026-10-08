<?php

namespace App\Controllers;

class AuthController
{
    public function loginForm(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['logged_in'])) {
            header('Location: ' . app_url('/dashboard'));
            exit;
        }

        $pageTitle = 'Login | SI Akademik';
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $validPassword = password_hash('admin123', PASSWORD_DEFAULT);

        if ($username !== 'admin' || !password_verify($password, $validPassword)) {
            $_SESSION['login_error'] = 'Username atau password salah.';
            header('Location: ' . app_url('/login'));
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Admin';
        $_SESSION['logged_in'] = true;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Selamat datang, Admin.'];

        header('Location: ' . app_url('/dashboard'));
        exit;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
        session_start();
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Anda telah logout.'];

        header('Location: ' . app_url('/login'));
        exit;
    }
}
