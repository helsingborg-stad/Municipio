<?php

namespace Municipio\ExternalContent\Filter\Transforms;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition as FilterDefinitionInterface;
use Municipio\ExternalContent\Filter\FilterDefinition\FilterDefinition;
use Municipio\ExternalContent\Filter\FilterDefinition\Rule;
use Municipio\ExternalContent\Filter\FilterDefinition\RuleSet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FilterDefinitionToTypesenseParamsTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();
        $this->assertInstanceOf(FilterDefinitionToTypesenseParams::class, $filterDefinitionToTypesenseParams);
    }

    /**
     * @testdox transform method returns string
     */
    public function testTransformMethodReturnsString()
    {
        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();
        $this->assertIsString($filterDefinitionToTypesenseParams->transform($this->getFilterDefinitionMock()));
    }

    /**
     * @testdox transform applies equals filter
     */
    public function testTransformAppliesBasicExactMatchFilter()
    {
        $rule             = new Rule('propertyName', 'propertyValue', Operator::EQUALS);
        $ruleSet          = new RuleSet([$rule]);
        $filterDefinition = new FilterDefinition([$ruleSet]);

        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();

        $this->assertEquals('filter_by=propertyName:=propertyValue', $filterDefinitionToTypesenseParams->transform($filterDefinition));
    }

    /**
     * @testdox transform applies not equals filter
     */
    public function testTransformAppliesBasicNotEqualFilter()
    {
        $rule             = new Rule('propertyName', 'propertyValue', Operator::NOT_EQUALS);
        $ruleSet          = new RuleSet([$rule]);
        $filterDefinition = new FilterDefinition([$ruleSet]);

        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();
        $this->assertEquals('filter_by=propertyName:!=propertyValue', $filterDefinitionToTypesenseParams->transform($filterDefinition));
    }

    /**
     * @testdox transform applies multiple exact match filters and separates them with "&&"
     */
    public function testTransformAppliesMultipleExactMatchFiltersAndSeparatesThemWithAnd()
    {
        $rule1            = new Rule('propertyName1', 'propertyValue1', Operator::EQUALS);
        $rule2            = new Rule('propertyName2', 'propertyValue2', Operator::EQUALS);
        $ruleSet          = new RuleSet([$rule1, $rule2]);
        $filterDefinition = new FilterDefinition([$ruleSet]);

        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();
        $transformed                       = $filterDefinitionToTypesenseParams->transform($filterDefinition);

        $this->assertEquals('filter_by=propertyName1:=propertyValue1&&propertyName2:=propertyValue2', $transformed);
    }

    /**
     * @testdox transform applies || match filters to separate ruleSets
     */
    public function testTransformAppliesOrMatchFiltersToSeparateRuleSets()
    {
        $rule1            = new Rule('propertyName1', 'propertyValue1', Operator::EQUALS);
        $ruleSet1         = new RuleSet([$rule1]);
        $rule2            = new Rule('propertyName2', 'propertyValue2', Operator::EQUALS);
        $ruleSet2         = new RuleSet([$rule2]);
        $filterDefinition = new FilterDefinition([$ruleSet1, $ruleSet2]);

        $filterDefinitionToTypesenseParams = new FilterDefinitionToTypesenseParams();
        $transformed                       = $filterDefinitionToTypesenseParams->transform($filterDefinition);

        $this->assertEquals('filter_by=(propertyName1:=propertyValue1)||(propertyName2:=propertyValue2)', $transformed);
    }

    private function getFilterDefinitionMock(): FilterDefinitionInterface|MockObject
    {
        return $this->createMock(FilterDefinitionInterface::class);
    }
}
