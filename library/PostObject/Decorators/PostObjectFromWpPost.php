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
        PostObjectInterface $postObject,
        private WP_Post $wpPost,
        private GetPermalink&GetCommentCount $wpService
    ) {
        parent::__construct($postObject);
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
    public function getContent(): string
    {
        return $this->wpPost->post_content;
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

    /**
     * @inheritDoc
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return strtotime($gmt ? $this->wpPost->post_date_gmt : $this->wpPost->post_date);
    }

    /**
     * @inheritDoc
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return strtotime($gmt ? $this->wpPost->post_modified_gmt : $this->wpPost->post_modified);
    }
}
