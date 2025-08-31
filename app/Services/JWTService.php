<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class JWTService
{
    public function signAccessToken(array $claims): string {
        $now = time();
        $ttl = (int) env('JWT_AT_TTL', 3600);

        $payload = array_merge([
            'iss' => env('JWT_ISS', 'https://auth.example.com'),
            'aud' => env('JWT_AUD', 'minyi-erp-web'),
            'iat' => $now,
            'exp' => $now + $ttl,
            'jti' => (string) Str::uuid(),
        ], $claims);

    

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function verify(string $jwt): object {
        return JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));
    }
}
