<?php

namespace App\Controllers;

use App\Core\AppLogger;
use App\Core\Auth;
use App\Core\Response;
use App\Models\User;
use JsonException;
use PDOException;

class AuthController
{
    public function register(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!preg_match('/^application\/json(?:\s*;|$)/i', $contentType)) {
            Response::json(415, false, 'Content-Type harus application/json.');
        }

        try {
            $input = json_decode(file_get_contents('php://input'), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            Response::json(400, false, 'Body harus berisi JSON yang valid.');
        }

        if (!is_object($input)) {
            Response::json(400, false, 'Body JSON harus berupa object.');
        }
        $input = get_object_vars($input);

        $errors = [];
        foreach (['username', 'email', 'password'] as $field) {
            if (!isset($input[$field]) || !is_string($input[$field]) || trim($input[$field]) === '') {
                $errors[$field] = ucfirst($field) . ' wajib diisi.';
            }
        }
        if ($errors !== []) {
            Response::json(422, false, 'Data registrasi tidak valid.', null, $errors);
        }

        $username = trim($input['username']);
        $email = trim($input['email']);
        if (mb_strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        }
        if (mb_strlen($email) > 100) {
            $errors['email'] = 'Email maksimal 100 karakter.';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Format email tidak valid.';
        }
        if ($errors !== []) {
            Response::json(422, false, 'Data registrasi tidak valid.', null, $errors);
        }

        try {
            $id = User::create([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
            ]);
            Response::json(201, true, 'Registrasi berhasil.', [
                'id' => $id,
                'username' => $username,
                'email' => $email,
            ]);
        } catch (\InvalidArgumentException $exception) {
            Response::json(422, false, $exception->getMessage(), null, ['email' => $exception->getMessage()]);
        } catch (PDOException $exception) {
            if ((string) $exception->getCode() === '23000') {
                Response::json(409, false, 'Username atau email sudah terdaftar.', null, [
                    'username' => 'Username atau email sudah digunakan.',
                    'email' => 'Username atau email sudah digunakan.',
                ]);
            }
            AppLogger::error('API gagal mendaftarkan pengguna', $exception);
            Response::json(500, false, 'Registrasi gagal karena gangguan server.');
        }
    }

    public function apiLogin(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!preg_match('/^application\/json(?:\s*;|$)/i', $contentType)) {
            Response::json(415, false, 'Content-Type harus application/json.');
        }

        try {
            $input = json_decode(file_get_contents('php://input'), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            Response::json(400, false, 'Body harus berisi JSON yang valid.');
        }

        if (!is_object($input)) {
            Response::json(400, false, 'Body JSON harus berupa object.');
        }

        $input = get_object_vars($input);
        $username = $input['username'] ?? null;
        $password = $input['password'] ?? null;
        if (!is_string($username) || trim($username) === '' || !is_string($password) || $password === '') {
            Response::json(422, false, 'Username dan password wajib diisi.', null, [
                'username' => 'Username wajib diisi.',
                'password' => 'Password wajib diisi.',
            ]);
        }

        try {
            $user = User::findByUsername(trim($username));
        } catch (PDOException $exception) {
            AppLogger::error('API gagal mencari pengguna untuk login', $exception);
            Response::json(500, false, 'Login gagal karena gangguan server.');
        }

        if ($user === null || !password_verify($password, $user['password'])) {
            Response::json(401, false, 'Username atau password salah.');
        }

        $token = Auth::generateToken([
            'id' => (int) $user['id'],
            'role' => $user['role'],
        ]);
        Response::json(200, true, 'Login berhasil.', [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 300,
            'role' => $user['role'],
        ]);
    }

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
        require __DIR__ . '/../../public/views/auth/login.php';
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        try {
            $user = User::findByUsername($username);
        } catch (PDOException $exception) {
            AppLogger::error('Web gagal mencari pengguna untuk login', $exception);
            $_SESSION['login_error'] = 'Login gagal karena gangguan server.';
            header('Location: ' . app_url('/login'));
            exit;
        }

        if ($user === null || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Username atau password salah.';
            header('Location: ' . app_url('/login'));
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Selamat datang, ' . $user['username'] . '.'];

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
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Anda telah logout.'];

        header('Location: ' . app_url('/login'));
        exit;
    }
}
