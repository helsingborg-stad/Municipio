<?php

namespace Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts;

interface FilterInterface
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSetInterface[]
     */
    public function getRuleSets(): array;
}
