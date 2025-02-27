<?php

namespace Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts;

interface FilterDefinition
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSet[]
     */
    public function getRuleSets(): array;
}
