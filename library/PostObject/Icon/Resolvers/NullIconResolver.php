<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\PostObject\Icon\IconInterface;

/**
 * Null icon resolver.
 */
class NullIconResolver implements IconResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(): ?IconInterface
    {
        return null;
    }
}
