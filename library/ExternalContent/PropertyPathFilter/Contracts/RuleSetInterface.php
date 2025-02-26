<?php

namespace Municipio\ExternalContent\PropertyPathFilter\Contracts;

use Municipio\ExternalContent\PropertyPathFilter\Contracts\Enums\Relation;

interface RuleSetInterface
{
    /**
     * Get the rules to apply.
     *
     * @return RuleInterface[]
     */
    public function getRules(): array;

    /**
     * Retrieves the relation to use when combining multiple RuleSets.
     *
     * @return Relation The relation object.
     */
    public function getRelation(): Relation;
}
