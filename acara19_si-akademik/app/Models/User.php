<?php

namespace App\Models;

require_once __DIR__ . '/Model.php';

class User extends Model
{
    /** @param array{username: string, email: string, password: string} $data */
    public static function create(array $data): int
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Format email tidak valid.');
        }

        $statement = static::connection()->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );
        $statement->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return (int) static::connection()->lastInsertId();
    }

    public static function findByUsername(string $username): ?array
    {
        $statement = static::connection()->prepare(
            'SELECT id, username, email, password, role, created_at FROM users WHERE username = :username'
        );
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }
}
