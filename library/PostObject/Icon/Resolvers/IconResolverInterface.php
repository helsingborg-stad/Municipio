<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\PostObject\Icon\IconInterface;

interface IconResolverInterface
{
    /**
     * Resolve icon.
     *
     * @return IconInterface|null
     */
    public function resolve(): ?IconInterface;
}
