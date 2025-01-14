<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CachedIconResolver
 *
 * This class is responsible for resolving icons with caching mechanism.
 */
class CachedIconResolver implements IconResolverInterface
{
    private static $cache = [];

    /**
     * Constructor.
     *
     * @param PostObjectInterface $postObject
     * @param IconResolverInterface $innerResolver
     */
    public function __construct(private PostObjectInterface $postObject, private IconResolverInterface $innerResolver)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): ?IconInterface
    {
        $cacheKey = "{$this->postObject->getBlogId()}_{$this->postObject->getId()}";

        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        return self::$cache[$cacheKey] = $this->innerResolver->resolve();
    }
}
