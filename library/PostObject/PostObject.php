<?php

namespace Municipio\PostObject;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\Image\ImageInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\WpGetPostTerms;

/**
 * PostObject
 */
class PostObject implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(private int $id, private GetCurrentBlogId|WpGetPostTerms $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name): mixed
    {
        if (isset($this->postObject->{$name})) {
            return $this->postObject->{$name};
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
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
    public function getArchiveDateTimestamp(): ?int
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        return 'date-time';
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getTerms(array $taxonomies): array
    {
        $terms = $this->wpService->wpGetPostTerms($this->getId(), $taxonomies);

        return is_array($terms) ? $terms : [];
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): BaseType
    {
        return new BaseType();
    }

    /**
     * @inheritDoc
     */
    public function getImage(): ?ImageInterface
    {
        return null;
    }
}
