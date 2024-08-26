<?php

namespace Municipio\ExternalContent\Taxonomy;

interface TaxonomyRegistrarInterface
{
    public function register(): void;
    public function getRegisteredTaxonomyItems(): array;
}
