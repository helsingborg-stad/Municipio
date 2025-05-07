<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Date\TimestampResolverInterface;

/**
 * PostObjectWithSeoRedirect class.
 *
 * Applies the SEO redirect to the post object permalink if a redirect is set.
 */
class PostObjectArchiveDateTimestamp extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private TimestampResolverInterface $timestampResolver
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateTimestamp(): ?int
    {
        return $this->timestampResolver->resolve();
    }
}
