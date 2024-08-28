<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Sources\SourceInterface;

interface TaxonomyItemsFactoryInterface
{
    /**
     * Create the taxonomies.
     *
     * @return TaxonomyItemInterface[] The created taxonomies.
     */
    public function create(SourceInterface $source): array;
}
