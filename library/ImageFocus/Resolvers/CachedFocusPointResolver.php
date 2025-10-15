<?php

namespace Municipio\ImageFocus\Resolver;

use Psr\SimpleCache\CacheInterface;

class CachedFocusPointResolver implements FocusPointResolverInterface
{
    public function __construct(
        private FocusPointResolverInterface $innerResolver,
        private CacheInterface $cache,
        private int $ttl = 86400 // 1 day
    ) {}

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        $cacheKey = $this->createCacheKey($filePath, $width, $height);

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $result = $this->innerResolver->resolve($filePath, $width, $height, $attachmentId);
        $this->cache->set($cacheKey, $result, $this->ttl);

        return $result;
    }

    private function createCacheKey(string $filePath, int $width, int $height): string
    {
        return 'focuspoint_' . md5($filePath . $width . $height);
    }
}