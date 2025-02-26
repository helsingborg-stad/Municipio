<?php

namespace Municipio\ExternalContent\PropertyPathFilter;

use Municipio\ExternalContent\PropertyPathFilter\Contracts\Enums\Relation;
use Municipio\ExternalContent\PropertyPathFilter\Contracts\RuleSetInterface;
use Municipio\ExternalContent\PropertyPathFilter\Contracts\RuleInterface;

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
     * @param RuleInterface[] $rules The rules to apply.
     * @param Relation $relation The relation to use when combining multiple RuleSets.
     */
    public function __construct(
        private array $rules,
        private Relation $relation = Relation::AND
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

    /**
     * @inheritDoc
     */
    public function getRelation(): Relation
    {
        return $this->relation;
    }
}
