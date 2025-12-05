<?php

namespace Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;
use Municipio\SchemaData\ExternalContent\Filter\GetValueByPathFromArray\GetValueByPathFromArray;

/**
 * Class SchemaObjectsFilterFromFilterDefinition
 */
class SchemaObjectsFilterFromFilterDefinition implements SchemaObjectsFilterInterface
{
    /**
     * SchemaObjectsFilterFromFilterDefinition constructor.
     *
     * @param FilterDefinition $filterDefinition
     */
    public function __construct(private FilterDefinition $filterDefinition)
    {
    }

    /**
     * @inheritDoc
     */
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
