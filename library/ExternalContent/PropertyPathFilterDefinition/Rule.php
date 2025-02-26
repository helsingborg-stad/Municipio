<?php

namespace Municipio\ExternalContent\PropertyPathFilterDefinition;

use Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts\Enums\Relation;
use Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts\RuleInterface;

/**
 * Rule class.
 *
 * Basic implementation of the RuleInterface.
 */
class Rule implements RuleInterface
{
    /**
     * Rule constructor.
     *
     * @param string $propertyPath
     * @param Operator $operator
     * @param string $value
     * @param Relation $relation
     */
    public function __construct(
        private string $propertyPath,
        private string $value,
        private Operator $operator = Operator::EQUALS,
        private Relation $relation = Relation::AND
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /**
     * @inheritDoc
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getRelation(): Relation
    {
        return $this->relation;
    }
}
