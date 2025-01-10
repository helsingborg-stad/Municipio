<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;

class CachedIconResolver implements IconResolverInterface
{
    private static $cache = [];

    public function __construct(private PostObjectInterface $postObject, private IconResolverInterface $innerResolver)
    {
    }

    public function resolve(): ?IconInterface
    {
        $cacheKey = (string)$this->postObject->getId();

        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        return self::$cache[$cacheKey] = $this->innerResolver->resolve();
    }
}
