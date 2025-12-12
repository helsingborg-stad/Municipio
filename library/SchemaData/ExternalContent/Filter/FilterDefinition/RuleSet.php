<?php

namespace Municipio\SchemaData\ExternalContent\Filter\FilterDefinition;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Relation;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Rule;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\RuleSet as RuleSetInterface;

/**
 * RuleSet class.
 *
 * Basic implementation of the RuleSetInterface.
 */
class RuleSet implements RuleSetInterface
{
    /**
     * RuleSet constructor.
     *
     * @param Rule[] $rules The rules to apply.
     * @param Relation $relation The relation to use when combining multiple RuleSets.
     */
    public function __construct(
        private array $rules,
        private Relation $relation = Relation::OR,
    ) {}

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @inheritDoc
     */
    public function getRelation(): Relation
    {
        return $this->relation;
    }
}
