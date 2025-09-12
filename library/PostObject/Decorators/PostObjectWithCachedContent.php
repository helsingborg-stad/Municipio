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
        $postId = $this->postObject->getId();
        if (!isset(self::$runtimeCache[$postId])) {
            self::$runtimeCache[$postId] = $this->postObject->getContent();
        }
        return self::$runtimeCache[$postId];
    }
}
