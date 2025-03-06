<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Rule as RuleInterface;

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
        private Operator $operator = Operator::EQUALS
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
}
