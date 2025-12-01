<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;

/**
 * CachedArchiveDateSourceResolver class.
 */
class CachedArchiveDateSourceResolver implements ArchiveDateSourceResolverInterface
{
    private static array $cache = [];

    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private ArchiveDateSourceResolverInterface $innerResolver
    ) {
    }

    /**
     * Resolve the archive date setting.
     */
    public function resolve(): string
    {
        $cacheKey = "{$this->postObject->getBlogId()}_{$this->postObject->getPostType()}";

        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        return self::$cache[$cacheKey] = (string) $this->innerResolver->resolve();
    }
}
