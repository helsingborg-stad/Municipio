<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;

/**
 * PostObjectWithOtherBlogId
 */
class PostObjectWithOtherBlogId extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(PostObjectInterface $inner, private int $blogId)
    {
        $this->postObject = $inner;
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->blogId;
    }
}
