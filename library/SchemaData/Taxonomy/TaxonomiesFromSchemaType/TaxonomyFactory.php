<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Taxonomy class represents a custom taxonomy in WordPress.
 */
class TaxonomyFactory implements TaxonomyFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(
        string $schemaType,
        string $schemaProperty,
        string $label,
        string $singularLabel,
        array $arguments = []
    ): TaxonomyInterface {
        return new Taxonomy($schemaType, $schemaProperty, $label, $singularLabel, $arguments);
    }
}
