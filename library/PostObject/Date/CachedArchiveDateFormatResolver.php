<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Date\ArchiveDateFormatResolverInterface;

/**
 * CachedArchiveDateFormatResolver class.
 */
class CachedArchiveDateFormatResolver implements ArchiveDateFormatResolverInterface
{
    private static array $cache = [];

    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private ArchiveDateFormatResolverInterface $innerResolver
    ) {
    }

    /**
     * Resolve the archive date format setting.
     */
    public function resolve(): string
    {
        $cacheKey = $this->postObject->getBlogId() . '_' . $this->postObject->getPostType();

        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        return self::$cache[$cacheKey] = $this->innerResolver->resolve();
    }
}
