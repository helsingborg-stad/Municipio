<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\Icon\Resolvers\IconResolverInterface;
use Municipio\PostObject\PostObjectInterface;

/**
 * IconResolvingPostObject
 */
class IconResolvingPostObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(PostObjectInterface $inner, private IconResolverInterface $iconResolver)
    {
        $this->postObject = $inner;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->iconResolver->resolve();
    }
}
