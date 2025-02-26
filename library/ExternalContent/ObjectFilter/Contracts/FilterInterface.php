<?php

namespace Municipio\ExternalContent\ObjectFilter\Contracts;

interface FilterInterface
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSetInterface[]
     */
    public function getRuleSets(): array;
}
