<?php

namespace Municipio\ExternalContent\Taxonomy;

interface TaxonomyItemsFactoryInterface
{
    /**
     * Create the taxonomies.
     *
     * @return TaxonomyItemInterface[] The created taxonomies.
     */
    public function create(): array;
}
