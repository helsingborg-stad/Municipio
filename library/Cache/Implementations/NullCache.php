<?php

namespace Municipio\Cache\Implementations;

use Municipio\Cache\CacheInterface;

class NullCache implements CacheInterface
{
    public function get(string $key, ?string $group = null): mixed
    {
        return null;
    }

    public function set(string $key, mixed $value, ?string $group = null, ?int $expire = null): void
    {
        return;
    }

    public function delete(string $key, ?string $group = null): void
    {
        return;
    }
}
