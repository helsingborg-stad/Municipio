<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use WP_Post;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetCommentCount;
use WpService\Contracts\GetPermalink;

/**
 * PostObject from WP_Post.
 */
class PostObjectFromWpPost implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private WP_Post $wpPost,
        private GetPermalink&GetCommentCount $wpService
    ) {
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

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->postObject->getBlogId();
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

    /**
     * @inheritDoc
     */
    public function getArchiveDateTimestamp(): ?int
    {
        return $this->postObject->getArchiveDateTimestamp();
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        return $this->postObject->getArchiveDateFormat();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        return $this->postObject->getSchemaProperty($property);
    }

    /**
     * @inheritDoc
     */
    public function getTerms(array $taxonomies): array
    {
        return $this->postObject->getTerms($taxonomies);
    }
}
