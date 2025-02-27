<?php

namespace Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts;

use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\Enums\Relation;

interface Rule
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
}
