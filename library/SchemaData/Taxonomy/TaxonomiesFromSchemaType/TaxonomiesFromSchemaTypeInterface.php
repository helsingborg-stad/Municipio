<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

interface TaxonomiesFromSchemaTypeInterface
{
    /**
     * Creates taxonomies for the given schema type.
     *
     * @param string $schemaType Schema type to generate taxonomies for.
     * @return TaxonomyInterface[] Array of taxonomy instances.
     */
    public function create(string $schemaType): array;
}
