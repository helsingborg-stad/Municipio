<?php

namespace Municipio\ExternalContent\Taxonomy;

interface TaxonomyItemsFactoryInterface
{
    /**
     * Create taxonomy items.
     *
     * @return TaxonomyItemInterface[] The taxonomy items.
     */
    public function createTaxonomyItems(): array;
}
