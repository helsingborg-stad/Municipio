<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Taxonomy class represents a custom taxonomy in WordPress.
 */
interface TaxonomyFactoryInterface
{
    /**
     * Creates a taxonomy based on the provided schema type and property.
     */
    public function create(
        string $schemaType,
        string $schemaProperty,
        array $objectTypes,
        string $label,
        string $singularLabel,
        array $arguments = []
    ): TaxonomyInterface;
}
