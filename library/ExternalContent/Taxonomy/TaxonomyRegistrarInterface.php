<?php

namespace Municipio\ExternalContent\Taxonomy;

interface TaxonomyRegistrarInterface
{
    public function register(TaxonomyItem $taxonomyItem): void;
    public function getRegisteredTaxonomyItems(): array;
}
