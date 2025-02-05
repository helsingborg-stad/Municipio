<?php

namespace Municipio\PostObject;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetCurrentBlogId;

/**
 * PostObject
 */
class PostObject implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(private GetCurrentBlogId $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->wpService->getCurrentBlogId();
    }

    /**
     * @inheritDoc
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateTimestamp(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        return 'Y-m-d H:i';
    }
}
