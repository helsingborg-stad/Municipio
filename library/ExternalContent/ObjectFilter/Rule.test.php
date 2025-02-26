<?php

namespace Municipio\ExternalContent\ObjectFilter;

use Municipio\ExternalContent\ObjectFilter\Contracts\Enums\Operator;
use Municipio\ExternalContent\ObjectFilter\Contracts\Enums\Relation;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $rule = new Rule('propertyPath', 'value');
        $this->assertInstanceOf(Rule::class, $rule);
    }

    /**
     * @testdox getPropertyPath() returns provided propertyPath
     */
    public function testGetPropertyPathReturnsPropertyPath()
    {
        $rule = new Rule('propertyPath', 'value');
        $this->assertEquals('propertyPath', $rule->getPropertyPath());
    }

    /**
     * @testdox getOperator() returns provided Operator
     */
    public function testGetOperatorReturnsOperatorEquals()
    {
        $rule = new Rule('propertyPath', 'value', Operator::EQUALS);
        $this->assertEquals(Operator::EQUALS, $rule->getOperator());
    }

    /**
     * @testdox getValue() returns provided value
     */
    public function testGetValueReturnsValue()
    {
        $rule = new Rule('propertyPath', 'value');
        $this->assertEquals('value', $rule->getValue());
    }

    /**
     * @testdox getRelation() returns provided Relation
     */
    public function testGetRelationReturnsRelation()
    {
        $rule = new Rule('propertyPath', 'value', Operator::EQUALS, Relation::AND);
        $this->assertEquals(Relation::AND, $rule->getRelation());
    }
}
