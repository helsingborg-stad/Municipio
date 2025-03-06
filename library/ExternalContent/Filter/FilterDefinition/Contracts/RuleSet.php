<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition\Contracts;

interface RuleSet
{
    /**
     * Get the rules to apply.
     *
     * @return Rule[]
     */
    public function getRules(): array;
}
