<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheVersion
{
    public static function key(string $namespace, string $key): string
    {
        return sprintf('%s:v%d:%s', $namespace, self::get($namespace), $key);
    }

    public static function bump(string $namespace): void
    {
        $versionKey = self::versionKey($namespace);
        $current = (int) Cache::get($versionKey, 1);
        Cache::forever($versionKey, $current + 1);
    }

    public static function bumpMany(array $namespaces): void
    {
        foreach ($namespaces as $namespace) {
            self::bump($namespace);
        }
    }

    private static function get(string $namespace): int
    {
        $versionKey = self::versionKey($namespace);

        return (int) Cache::rememberForever($versionKey, fn () => 1);
    }

    private static function versionKey(string $namespace): string
    {
        return 'cache:version:' . $namespace;
    }
}
