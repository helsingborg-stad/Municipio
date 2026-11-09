<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Base class for taxonomy decorators. Delegates everything to the decorated taxonomy,
 * so subclasses only override what they change.
 */
abstract class TaxonomyDecorator implements TaxonomyInterface
{
    /**
     * @param TaxonomyInterface $inner The decorated taxonomy.
     */
    public function __construct(protected TaxonomyInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->inner->getName();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaType(): string
    {
        return $this->inner->getSchemaType();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(): string
    {
        return $this->inner->getSchemaProperty();
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypes(): array
    {
        return $this->inner->getObjectTypes();
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->inner->getArguments();
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->inner->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function getSingularLabel(): string
    {
        return $this->inner->getSingularLabel();
    }

    /**
     * @inheritDoc
     */
    public function formatTermValue(mixed $value, array $schema): string|array|null
    {
        return $this->inner->formatTermValue($value, $schema);
    }
}
