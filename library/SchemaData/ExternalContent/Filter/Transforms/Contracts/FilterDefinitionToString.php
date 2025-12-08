<?php

namespace Municipio\SchemaData\ExternalContent\Filter\Transforms\Contracts;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;

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
