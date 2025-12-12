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
    public function __construct(
        private FilterDefinition $filterDefinition,
    ) {}

    /**
     * @inheritDoc
     */
    public function filter(array $schemaObjects): array
    {
        $getValueByPathFromArray = new GetValueByPathFromArray();
        $results = [];
        foreach ($this->filterDefinition->getRuleSets() as $ruleSet) {
            $relation = method_exists($ruleSet, 'getRelation') ? $ruleSet->getRelation() : null;
            $rules = $ruleSet->getRules();
            if (empty($rules)) {
                continue;
            }
            $filtered = array_filter($schemaObjects, function ($schemaObject) use ($rules, $relation, $getValueByPathFromArray) {
                if ($relation === null || $relation === \Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Relation::OR) {
                    // OR: match if any rule matches
                    foreach ($rules as $rule) {
                        $value = $getValueByPathFromArray->getValueByPath($schemaObject->toArray(), $rule->getPropertyPath());
                        if ($rule->getOperator() === Operator::EQUALS && $value === $rule->getValue()) {
                            return true;
                        }
                    }
                    return false;
                } else {
                    // AND: match if all rules match
                    foreach ($rules as $rule) {
                        $value = $getValueByPathFromArray->getValueByPath($schemaObject->toArray(), $rule->getPropertyPath());
                        if (!($rule->getOperator() === Operator::EQUALS && $value === $rule->getValue())) {
                            return false;
                        }
                    }
                    return true;
                }
            });
            foreach ($filtered as $obj) {
                $results[spl_object_hash($obj)] = $obj;
            }
        }
        return array_values($results);
    }
}
