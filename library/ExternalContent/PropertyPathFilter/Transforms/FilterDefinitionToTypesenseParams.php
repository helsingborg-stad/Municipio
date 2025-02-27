<?php

namespace Municipio\ExternalContent\PropertyPathFilter\Transforms;

use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\FilterDefinition;
use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\Rule;
use Municipio\ExternalContent\PropertyPathFilter\FilterDefinition\Contracts\RuleSet;
use Municipio\ExternalContent\PropertyPathFilter\Transforms\Contracts\FilterDefinitionToString;

/**
 * Transforms a FilterDefinition into a Typesense-compatible filter string.
 */
class FilterDefinitionToTypesenseParams implements FilterDefinitionToString
{
    /**
     * Transforms a FilterDefinition into a Typesense-compatible filter string.
     *
     * @param FilterDefinition $filterDefinition
     * @return string
     */
    public function transform(FilterDefinition $filterDefinition): string
    {
        $ruleSets            = $filterDefinition->getRuleSets();
        $hasMultipleRuleSets = count($ruleSets) > 1;
        $filterStrings       = [];

        foreach ($ruleSets as $ruleSet) {
            $filterStrings[] = $this->formatRuleSet($ruleSet, $hasMultipleRuleSets);
        }

        // Join multiple rule set strings with the logical OR operator.
        return implode('||', $filterStrings);
    }

    /**
     * Formats a single RuleSet into a Typesense-compatible string.
     *
     * @param RuleSet $ruleSet
     * @param bool $wrapWithParentheses Whether to wrap the rule set in parentheses (if multiple rule sets exist).
     * @return string
     */
    private function formatRuleSet(RuleSet $ruleSet, bool $wrapWithParentheses): string
    {
        $ruleStrings = [];

        foreach ($ruleSet->getRules() as $rule) {
            $ruleStrings[] = $this->formatRule($rule);
        }

        // Combine all rules in the set with the logical AND operator.
        $ruleSetString = implode('&&', $ruleStrings);

        if ($wrapWithParentheses) {
            $ruleSetString = "($ruleSetString)";
        }

        return $ruleSetString;
    }

    /**
     * Formats a single Rule into a Typesense-compatible string.
     *
     * @param Rule $rule
     * @return string
     */
    private function formatRule(Rule $rule): string
    {
        $operator = $this->mapOperatorToTypesense($rule->getOperator());
        return "{$rule->getPropertyPath()}:{$operator}{$rule->getValue()}";
    }

    /**
     * Maps a filter operator to its Typesense equivalent.
     *
     * @param Operator $operator
     * @return string
     */
    private function mapOperatorToTypesense(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUALS      => '=',
            Operator::NOT_EQUALS  => '!=',
        };
    }
}
