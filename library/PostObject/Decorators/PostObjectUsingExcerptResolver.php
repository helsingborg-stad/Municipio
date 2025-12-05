<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\ExcerptResolver\ExcerptResolver;
use Municipio\PostObject\ExcerptResolver\ExcerptResolverInterface;
use Municipio\PostObject\PostObjectInterface;

/**
 * PostObjectUsingExcerptResolver
 */
class PostObjectUsingExcerptResolver extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private ExcerptResolverInterface $excerptResolver,
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getExcerpt(): string
    {
        return $this->excerptResolver->resolveExcerpt($this->postObject);
    }
}
