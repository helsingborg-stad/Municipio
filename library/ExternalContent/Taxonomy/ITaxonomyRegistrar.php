<?php

namespace Municipio\ExternalContent\Taxonomy;

interface ITaxonomyRegistrar
{
    public function register(): void;
    public function getRegisteredTaxonomyItems(): array;
}
