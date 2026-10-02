<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static string $secretKey = 'ITcampus-SaintMichel-Annecy-cle-secrete-JWT-2026';
    private static string $algorithm = 'HS256';

    public static function generateToken($data, $expiry = 3600): string
    {
        $issuedAt = time();

        $payload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $expiry,
            'data' => $data
        ];

        return JWT::encode(
            $payload,
            self::$secretKey,
            self::$algorithm
        );
    }

    public static function validateToken($token)
    {
        try {
            return JWT::decode(
                $token,
                new Key(self::$secretKey, self::$algorithm)
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}