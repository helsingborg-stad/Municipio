<?php

namespace Municipio\ExternalContent\ObjectFilter;

use Municipio\ExternalContent\ObjectFilter\Contracts\Enums\Relation;
use Municipio\ExternalContent\ObjectFilter\Contracts\RuleInterface;
use PHPUnit\Framework\TestCase;

class RuleSetTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules());
        $this->assertInstanceOf(RuleSet::class, $ruleSet);
    }

    /**
     * @testdox class can not be instantiated with empty rules
     */
    public function testCanBeInstantiatedWithEmptyRules()
    {
        $this->expectException(\InvalidArgumentException::class);
        $ruleSet = new RuleSet([]);
    }

    /**
     * @testdox getRules() returns provided rules
     */
    public function testGetRulesReturnsRules()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules(2));
        $this->assertCount(2, $ruleSet->getRules());
    }

    /**
     * @testdox getRelation() returns provided relation
     */
    public function testGetRelationReturnsRelation()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules(), Relation::OR);
        $this->assertEquals(Relation::OR, $ruleSet->getRelation());
    }

    private function getArrayOfRules(int $nbrOfRulesInArray = 1): array
    {
        return array_fill(0, $nbrOfRulesInArray, $this->createMock(RuleInterface::class));
    }
}
