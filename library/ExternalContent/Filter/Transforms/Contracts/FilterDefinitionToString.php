<?php

namespace Municipio\ExternalContent\Filter\Transforms\Contracts;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;

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
