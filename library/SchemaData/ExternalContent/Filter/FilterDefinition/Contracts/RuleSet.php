<?php

namespace Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Relation;

interface RuleSet
{
    /**
     * Get the rules to apply.
     *
     * @return Rule[]
     */
    public function getRules(): array;

    /**
     * Get the relation to use between rules.
     *
     * @return Relation
     */
    public function getRelation(): Relation;
}
