<?php

namespace Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts;

interface FilterInterface
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSetInterface[]
     */
    public function getRuleSets(): array;
}
