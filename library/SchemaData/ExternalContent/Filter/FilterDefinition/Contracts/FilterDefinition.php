<?php

namespace Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts;

interface FilterDefinition
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSet[]
     */
    public function getRuleSets(): array;
}
