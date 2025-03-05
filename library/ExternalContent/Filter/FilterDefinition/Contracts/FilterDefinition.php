<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition\Contracts;

interface FilterDefinition
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSet[]
     */
    public function getRuleSets(): array;
}
