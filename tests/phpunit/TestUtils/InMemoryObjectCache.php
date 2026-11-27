<?php

declare(strict_types=1);

namespace Municipio\TestUtils;

use WpService\Contracts\WpCacheAdd;
use WpService\Contracts\WpCacheDelete;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheIncr;
use WpService\Contracts\WpCacheSet;

/**
 * In-memory object cache for tests. Records every write so tests can check what was stored and when.
 */
final class InMemoryObjectCache implements WpCacheGet, WpCacheSet, WpCacheAdd, WpCacheDelete, WpCacheIncr
{
    /** @var array<string, array<int|string, mixed>> */
    private array $storage = [];

    /** @var list<array{key: int|string, group: string, data: mixed, expire: int}> */
    private array $writes = [];

    // @mago-expect lint:no-boolean-flag-parameter
    public function wpCacheGet(int|string $key, string $group = '', bool $force = false, ?bool &$found = null): mixed
    {
        $found = array_key_exists($key, $this->storage[$group] ?? []);

        return $found ? $this->storage[$group][$key] : false;
    }

    public function wpCacheSet(int|string $key, mixed $data, string $group = '', int $expire = 0): bool
    {
        $this->storage[$group][$key] = $data;
        $this->writes[] = ['key' => $key, 'group' => $group, 'data' => $data, 'expire' => $expire];

        return true;
    }

    public function wpCacheAdd(int|string $key, mixed $data, string $group = '', int $expire = 0): bool
    {
        if (array_key_exists($key, $this->storage[$group] ?? [])) {
            return false;
        }

        return $this->wpCacheSet($key, $data, $group, $expire);
    }

    public function wpCacheDelete(int|string $key, string $group = ''): bool
    {
        $existed = array_key_exists($key, $this->storage[$group] ?? []);
        unset($this->storage[$group][$key]);

        return $existed;
    }

    public function wpCacheIncr(int|string $key, int $offset = 1, string $group = ''): int|false
    {
        $value = $this->storage[$group][$key] ?? null;

        if (!is_int($value)) {
            return false;
        }

        return $this->storage[$group][$key] = $value + $offset;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getGroup(string $group): array
    {
        return $this->storage[$group] ?? [];
    }

    /**
     * @return list<array{key: int|string, group: string, data: mixed, expire: int}>
     */
    public function getWrites(): array
    {
        return $this->writes;
    }

    public function clearWrites(): void
    {
        $this->writes = [];
    }
}
