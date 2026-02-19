<?php

namespace Municipio\PostObject\Decorators;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WpService\Contracts\ApplyFilters;

/**
 * Decorator that applies WordPress filters to all PostObject method return values.
 */
class PostObjectWithFilters extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private ApplyFilters $wpService,
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getId',
            $this->postObject->getId(),
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getTitle',
            $this->postObject->getTitle(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getContent',
            $this->postObject->getContent(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getExcerpt(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getExcerpt',
            $this->postObject->getExcerpt(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getContentHeadings(): array
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getContentHeadings',
            $this->postObject->getContentHeadings(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getPermalink',
            $this->postObject->getPermalink(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getCommentCount(): int
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getCommentCount',
            $this->postObject->getCommentCount(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getPostType(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getPostType',
            $this->postObject->getPostType(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getBlogId(): int
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getBlogId',
            $this->postObject->getBlogId(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?IconInterface
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getIcon',
            $this->postObject->getIcon(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getPublishedTime(bool $gmt = false): int
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getPublishedTime',
            $this->postObject->getPublishedTime($gmt),
            $this->postObject,
            $gmt,
        );
    }

    /**
     * @inheritDoc
     */
    public function getModifiedTime(bool $gmt = false): int
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getModifiedTime',
            $this->postObject->getModifiedTime($gmt),
            $this->postObject,
            $gmt,
        );
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateTimestamp(): ?int
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getArchiveDateTimestamp',
            $this->postObject->getArchiveDateTimestamp(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getArchiveDateFormat(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getArchiveDateFormat',
            $this->postObject->getArchiveDateFormat(),
            $this->postObject,
        );
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        $type = $this->getSchema()->getType();

        $value = $this->wpService->applyFilters(
            'Municipio/PostObject/getSchemaProperty',
            $this->postObject->getSchemaProperty($property),
            $this->postObject,
            $property,
            $type,
        );

        $value = $this->wpService->applyFilters(
            'Municipio/PostObject/getSchemaProperty/' . $type . '/' . $property,
            $value,
            $this->postObject,
            $property,
            $type,
        );

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): BaseType
    {
        $type = $this->postObject->getSchema()->getType();

        $value = $this->wpService->applyFilters(
            'Municipio/PostObject/getSchema',
            $this->postObject->getSchema(),
            $this->postObject,
            $type,
        );

        $value = $this->wpService->applyFilters(
            'Municipio/PostObject/getSchema/' . $type,
            $value,
            $this->postObject,
            $type,
        );

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getTerms(array $taxonomies): array
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getTerms',
            $this->postObject->getTerms($taxonomies),
            $this->postObject,
            $taxonomies,
        );
    }

    /**
     * @inheritDoc
     */
    public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/getImage',
            $this->postObject->getImage($width, $height),
            $this->postObject,
            $width,
            $height,
        );
    }

    /**
     * @inheritDoc
     */
    public function __get(string $key): mixed
    {
        return $this->wpService->applyFilters(
            'Municipio/PostObject/__get',
            $this->postObject->__get($key),
            $this->postObject,
            $key,
        );
    }
}
