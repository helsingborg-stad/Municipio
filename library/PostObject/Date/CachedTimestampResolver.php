<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetThemeMod;

/**
 * CachedTimestampResolver class.
 */
class CachedTimestampResolver implements TimestampResolverInterface
{
    private static array $idCache = [];

    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private GetThemeMod&GetPostMeta $wpService,
        private TimestampResolverInterface $innerResolver
    ) {
    }

    /**
     * Resolve the timestamp.
     *
     * @return int
     */
    public function resolve(): ?int
    {
        $cacheKey = "{$this->postObject->getBlogId()}_{$this->postObject->getId()}_{$this->postObject->getPublishedTime()}";

        if (array_key_exists($cacheKey, self::$idCache)) {
            return self::$idCache[$cacheKey];
        }

        return self::$idCache[$cacheKey] = $this->innerResolver->resolve();
    }
}
