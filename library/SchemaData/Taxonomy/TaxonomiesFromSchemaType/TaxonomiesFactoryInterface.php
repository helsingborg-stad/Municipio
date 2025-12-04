<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

interface TaxonomiesFactoryInterface
{
    /**
     * Create taxonomies to be used for Schemas.
     *
     * @return TaxonomyInterface[] An array of taxonomies, each implementing TaxonomyInterface.
     */
    public function create(): array;
}
