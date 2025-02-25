<?php

namespace Municipio\ExternalContent\ObjectFilter\Contracts;

use Municipio\ExternalContent\ObjectFilter\Contracts\Enums\Relation;

interface RuleSet
{
    /**
     * Get the rules to apply.
     *
     * @return Rule[]
     */
    public function getRules(): array;

    /**
     * Retrieves the relation to use when combining multiple RuleSets.
     *
     * @return Relation The relation object.
     */
    public function getRelation(): Relation;
}
