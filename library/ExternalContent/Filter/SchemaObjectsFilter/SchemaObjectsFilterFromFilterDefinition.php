<?php

namespace Municipio\ExternalContent\Filter\SchemaObjectsFilter;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;
use Municipio\ExternalContent\Filter\GetValueByPathFromArray\GetValueByPathFromArray;

class SchemaObjectsFilterFromFilterDefinition implements SchemaObjectsFilterInterface
{
    public function __construct(private FilterDefinition $filterDefinition)
    {
    }

    public function filter(array $schemaObjects): array
    {
        $getValueByPathFromArray = new GetValueByPathFromArray();

        foreach ($this->filterDefinition->getRuleSets() as $ruleSet) {
            foreach ($ruleSet->getRules() as $rule) {
                $schemaObjects = array_filter($schemaObjects, function ($schemaObject) use ($rule, $getValueByPathFromArray) {
                    $value = $getValueByPathFromArray->getValueByPath($schemaObject->toArray(), $rule->getPropertyPath());

                    if ($rule->getOperator() === Operator::EQUALS) {
                        return $value === $rule->getValue();
                    }

                    return false;
                });
            }
        }

                return $schemaObjects;
    }
}
