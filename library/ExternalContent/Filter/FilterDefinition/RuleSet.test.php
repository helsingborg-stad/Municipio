<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Relation;
use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\Rule;
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
        new RuleSet([]);
    }

    /**
     * @testdox getRules() returns provided rules
     */
    public function testGetRulesReturnsRules()
    {
        $ruleSet = new RuleSet($this->getArrayOfRules(2));
        $this->assertCount(2, $ruleSet->getRules());
    }

    private function getArrayOfRules(int $nbrOfRulesInArray = 1): array
    {
        return array_fill(0, $nbrOfRulesInArray, $this->createMock(Rule::class));
    }
}
