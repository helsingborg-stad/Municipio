<?php

namespace Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts;

interface RuleSet
{
    /**
     * Get the rules to apply.
     *
     * @return Rule[]
     */
    public function getRules(): array;
}
