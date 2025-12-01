<?php

namespace Municipio\SchemaData\ExternalContent\Filter\FilterDefinition;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\RuleSet as RuleSetInterface;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Rule;

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
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
