<?php

namespace Municipio\SchemaData\ExternalContent\Filter\FilterDefinition;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Relation;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Rule;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class RuleSetTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules());
        $this->assertInstanceOf(RuleSet::class, $ruleSet);
    }

    #[TestDox('getRules() returns provided rules')]
    public function testGetRulesReturnsRules()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules(2));
        $this->assertCount(2, $ruleSet->getRules());
    }

    #[TestDox('getRelation() returns provided relation')]
    public function testGetRelationReturnsRelation()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules(), Relation::AND);
        $this->assertEquals(Relation::AND, $ruleSet->getRelation());
    }

    #[TestDox('getRelation() returns OR as default relation')]
    public function testGetRelationReturnsDefaultRelation()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules());
        $this->assertEquals(Relation::OR, $ruleSet->getRelation());
    }

    private function getArrayOfRules(int $nbrOfRulesInArray = 1): array
    {
        return array_fill(0, $nbrOfRulesInArray, $this->createMock(Rule::class));
    }
}
