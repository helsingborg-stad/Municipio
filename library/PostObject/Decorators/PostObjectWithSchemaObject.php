<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;

/**
 * Decorator for PostObject that adds schema object functionality.
 *
 * @package Municipio\PostObject\Decorators
 */
class PostObjectWithSchemaObject implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private SchemaObjectFromPostInterface $schemaObjectFromPost
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        if ($property === '@type') {
            return $this->getSchemaObject()->getType();
        }

        return $this->getSchemaObject()->getProperty($property);
    }

    /**
     * Get the schema object.
     *
     * @return BaseType
     */
    private function getSchemaObject(): BaseType
    {
        static $schemaObject = null;

        if ($schemaObject === null) {
            $schemaObject                   = $this->schemaObjectFromPost->create($this->postObject);
            $this->postObject->schemaObject = $schemaObject;
        }

        return $schemaObject;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name): mixed
    {
        if ($name === 'schemaObject') {
            trigger_error('Deprecated: Use getSchemaObject() instead.', E_USER_DEPRECATED);
            return $this->getSchemaObject();
        }

        return $this->postObject->$name;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getId(): int
    {
        return $this->postObject->getId();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTitle(): string
    {
        return $this->postObject->getTitle();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getPermalink(): string
    {
        return $this->postObject->getPermalink();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getCommentCount(): int
    {
        return $this->postObject->getCommentCount();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getPostType(): string
    {
        return $this->postObject->getPostType();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getBlogId(): int
    {
        return $this->postObject->getBlogId();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getIcon(): ?IconInterface
    {
        return $this->postObject->getIcon();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return $this->postObject->getPublishedTime($gmt);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return $this->postObject->getModifiedTime($gmt);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getArchiveDateTimestamp(): ?int
    {
        return $this->postObject->getArchiveDateTimestamp();
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getArchiveDateFormat(): string
    {
        return $this->postObject->getArchiveDateFormat();
    }
}
