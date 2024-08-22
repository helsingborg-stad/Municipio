<?php

namespace Municipio\ExternalContent\Taxonomy;

interface TaxonomyItemsFactoryInterface
{
    /**
     * Create the taxonomies.
     *
     * @return ITaxonomyItem[] The created taxonomies.
     */
    public function create(): array;
}
