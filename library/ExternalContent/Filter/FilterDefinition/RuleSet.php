<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\RuleSet as RuleSetInterface;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Rule;

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
        if (empty($rules)) {
            throw new \InvalidArgumentException('Rules must not be empty.');
        }
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
