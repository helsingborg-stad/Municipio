<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;

/**
 * PostObjectWithCachedContent class.
 *
 * Caches the content of the post object to improve performance.
 */
class PostObjectWithCachedContent extends AbstractPostObjectDecorator implements PostObjectInterface
{
    private static array $runtimeCache = [];

    /**
     * Constructor.
     */
    public function __construct(PostObjectInterface $postObject)
    {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $blogId   = get_current_blog_id();
        $postId   = $this->postObject->getId();
        $cacheKey = $blogId . '-' . $postId;

        if (!isset(self::$runtimeCache[$cacheKey])) {
            self::$runtimeCache[$cacheKey] = $this->postObject->getContent();
        }

        return self::$runtimeCache[$cacheKey];
    }
}
