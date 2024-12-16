<?php

namespace Municipio\PostObject\Decorators;

use WP_Post;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetCommentCount;
use WpService\Contracts\GetPermalink;

/**
 * PostObject from WP_Post.
 */
class PostObjectFromWpPost extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $inner,
        private WP_Post $wpPost,
        private GetPermalink&GetCommentCount $wpService
    ) {
        $this->postObject = $inner;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->wpPost->ID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->wpPost->post_title;
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->wpService->getPermalink($this->wpPost);
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->wpService->getCommentCount($this->getId())['approved'];
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->wpPost->post_type;
    }
}
