<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyInterface;
use WP_Term;

interface TermFactoryInterface
{
    /**
     * Create WP_Term objects from taxonomy and schema data.
     *
     * @param TaxonomyInterface $taxonomy The taxonomy to create terms for.
     * @param array $schema The schema data containing the properties to extract.
     *
     * @return WP_Term[] An array of WP_Term objects.
     */
    public function create(TaxonomyInterface $taxonomy, array $schema): array;
}
