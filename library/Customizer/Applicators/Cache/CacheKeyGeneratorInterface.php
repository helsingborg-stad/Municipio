<?php

namespace Municipio\Customizer\Applicators\Cache;

/**
 * Interface for generating cache keys
 */
interface CacheKeyGeneratorInterface
{
    /**
     * Generate a cache key based on customizer state and content signature
     *
     * @return string The generated cache key
     */
    public function generateCacheKey(): string;
}