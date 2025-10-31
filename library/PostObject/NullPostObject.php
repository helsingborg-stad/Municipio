<?php

namespace Municipio\PostObject;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Thing;

/**
 * NullPostObject
 *
 * A PostObject implementation that represents a non-existing or null post.
 * This class is useful for testing or scenarios where a PostObject is required.
 */
class NullPostObject implements PostObjectInterface
{
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
    public function getContent(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getExcerpt(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getContentHeadings(): array
    {
        return [];
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
        return 0;
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
        return '';
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
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): BaseType
    {
        return new Thing();
    }

    /**
     * @inheritDoc
     */
    public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $key): mixed
    {
        return null;
    }
}
