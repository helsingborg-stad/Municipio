<?php

namespace Municipio\ExternalContent\PropertyPathFilter\Contracts;

interface FilterInterface
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSetInterface[]
     */
    public function getRuleSets(): array;
}
