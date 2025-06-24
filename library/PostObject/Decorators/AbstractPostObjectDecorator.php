<?php

namespace Municipio\PostObject\Decorators;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;

/**
 * AbstractPostObjectDecorator class.
 */
abstract class AbstractPostObjectDecorator implements PostObjectInterface
{
    protected PostObjectInterface $postObject;

    /**
     * AbstractPostObjectDecorator constructor.
     *
     * @param PostObjectInterface $postObject The post object to decorate.
     */
    public function __construct(PostObjectInterface $postObject)
    {
        $this->postObject = $postObject;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name): mixed
    {
        return $this->postObject->__get($name);
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->postObject->getId();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->postObject->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->postObject->getContent();
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->postObject->getPermalink();
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->postObject->getCommentCount();
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
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
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }

    /**
     * @inheritDoc
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return $this->postObject->getPublishedTime($gmt);
    }

    /**
     * @inheritDoc
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return $this->postObject->getModifiedTime($gmt);
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
    public function getSchema(): BaseType
    {
        return $this->postObject->getSchema();
    }

    /**
     * @inheritDoc
     */
    public function getTerms(array $taxonomies): array
    {
        return $this->postObject->getTerms($taxonomies);
    }

    /**
     * @inheritDoc
     */
    public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
    {
        return $this->postObject->getImage($width, $height);
    }
}
