<?php

namespace Municipio\Controller\Navigation\Cache;

interface CacheManagerInterface
{
    public function getCache(string $key, bool $persistent = true): mixed;
    public function setCache(string $key, mixed $data, bool $persistent = true): bool;
    public function setCacheGroup(string $newCacheGroup): bool;
}