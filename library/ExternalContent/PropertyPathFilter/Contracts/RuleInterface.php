<?php

namespace Municipio\ExternalContent\PropertyPathFilter\Contracts;

use Municipio\ExternalContent\PropertyPathFilter\Contracts\Enums\Operator;
use Municipio\ExternalContent\PropertyPathFilter\Contracts\Enums\Relation;

interface RuleInterface
{
    /**
     * Get the property path to the target property on object.
     *
     * @return string
     */
    public function getPropertyPath(): string;

    /**
     * Get the operator to use when comparing the value.
     *
     * @return Operator
     */
    public function getOperator(): Operator;

    /**
     * Get the value to compare with.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Get the relation to use when combining multiple rules.
     *
     * @return Relation
     */
    public function getRelation(): Relation;
}
