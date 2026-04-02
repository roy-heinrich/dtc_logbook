<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class RealtimeToken
{
    public static function issue(int $adminId, string $channel, int $ttlSeconds = 60): string
    {
        $payload = [
            'sub' => $adminId,
            'ch' => $channel,
            'exp' => Carbon::now()->addSeconds($ttlSeconds)->timestamp,
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $payloadEncoded = self::base64UrlEncode($payloadJson ?: '{}');
        $signature = hash_hmac('sha256', $payloadEncoded, self::appKey(), true);

        return $payloadEncoded . '.' . self::base64UrlEncode($signature);
    }

    private static function appKey(): string
    {
        $appKey = (string) config('app.key', '');

        if ($appKey === '') {
            return '';
        }

        if (str_starts_with($appKey, 'base64:')) {
            return base64_decode(substr($appKey, 7)) ?: '';
        }

        return $appKey;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}