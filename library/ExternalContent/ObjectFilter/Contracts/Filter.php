<?php

namespace Municipio\ExternalContent\ObjectFilter\Contracts;

interface Filter
{
    /**
     * Get the rulesets to apply.
     *
     * @return RuleSet[]
     */
    public function getRuleSets(): array;
}
