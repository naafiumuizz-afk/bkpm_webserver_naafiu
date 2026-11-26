<?php

namespace App\Core;

use JsonException;

class Auth
{
    private const SECRET = 'kunci_rahasia_workshop_2026';
    private const TOKEN_TTL = 300;

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function generateToken(array $payload): string
    {
        $header = self::base64url(json_encode(
            ['alg' => 'HS256', 'typ' => 'JWT'],
            JSON_THROW_ON_ERROR
        ));
        $payload['exp'] = time() + self::TOKEN_TTL;
        $encodedPayload = self::base64url(json_encode($payload, JSON_THROW_ON_ERROR));
        $unsignedToken = $header . '.' . $encodedPayload;
        $signature = self::base64url(hash_hmac('sha256', $unsignedToken, self::SECRET, true));

        return $unsignedToken . '.' . $signature;
    }

    public static function validateToken(string $token): array|false
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$encodedHeader, $encodedPayload, $signature] = $parts;
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $encodedHeader)
            || !preg_match('/^[A-Za-z0-9_-]+$/', $encodedPayload)
            || !preg_match('/^[A-Za-z0-9_-]+$/', $signature)) {
            return false;
        }

        try {
            $header = json_decode(
                self::decodeBase64url($encodedHeader),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            $payload = json_decode(
                self::decodeBase64url($encodedPayload),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            return false;
        }

        if (!is_array($header)
            || ($header['alg'] ?? null) !== 'HS256'
            || !is_array($payload)
            || !isset($payload['exp'])
            || !is_int($payload['exp'])
            || $payload['exp'] <= time()) {
            return false;
        }

        $expectedSignature = self::base64url(
            hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, self::SECRET, true)
        );

        return hash_equals($expectedSignature, $signature) ? $payload : false;
    }

    public static function requireAuth(): array
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (!is_array($headers)) {
            $headers = [];
        }
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        foreach ($headers as $name => $value) {
            if (strcasecmp((string) $name, 'Authorization') === 0) {
                $authorization = $value;
                break;
            }
        }

        if (!is_string($authorization)
            || !preg_match('/^Bearer\s+([A-Za-z0-9._-]+)$/i', $authorization, $matches)) {
            Response::json(401, false, 'Token tidak ditemukan.');
        }

        $payload = self::validateToken($matches[1]);
        if ($payload === false) {
            Response::json(401, false, 'Token tidak valid atau kedaluwarsa.');
        }

        return $payload;
    }

    public static function requireAdmin(): array
    {
        $payload = self::requireAuth();
        if (($payload['role'] ?? null) !== 'admin') {
            Response::json(403, false, 'Akses ditolak, hanya admin yang diizinkan.');
        }

        return $payload;
    }

    private static function decodeBase64url(string $data): string
    {
        $decoded = base64_decode(
            strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4),
            true
        );

        if ($decoded === false) {
            throw new JsonException('Segmen token tidak valid.');
        }

        return $decoded;
    }
}
