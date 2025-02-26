<?php

namespace Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts;

use Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts\Enums\Relation;

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
