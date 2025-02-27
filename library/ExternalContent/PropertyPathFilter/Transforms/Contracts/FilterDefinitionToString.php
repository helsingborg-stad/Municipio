<?php

namespace Municipio\ExternalContent\PropertyPathFilter\Transforms\Contracts;

use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\FilterDefinition;

interface FilterDefinitionToString
{
    /**
     * Get filter definition as string.
     *
     * @param FilterDefinition $filterDefinition
     * @return string
     */
    public function transform(FilterDefinition $filterDefinition): string;
}
