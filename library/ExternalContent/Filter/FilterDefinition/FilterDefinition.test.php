<?php

namespace Municipio\ExternalContent\Filter\FilterDefinition;

use Municipio\ExternalContent\Filter\FilterDefinition\Contracts\RuleSet;
use PHPUnit\Framework\TestCase;

class FilterDefinitionTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $filterDefinition = new FilterDefinition($this->getArrayOfRuleSets());
        $this->assertInstanceOf(FilterDefinition::class, $filterDefinition);
    }

    /**
     * @testdox getRuleSets() returns provided ruleSets
     */
    public function testGetRuleSetsReturnsRuleSets()
    {
        $ruleSets         = $this->getArrayOfRuleSets(2);
        $filterDefinition = new FilterDefinition($ruleSets);
        $this->assertCount(2, $filterDefinition->getRuleSets());
    }

    private function getArrayOfRuleSets(int $nbrOfRuleSetsInArray = 1): array
    {
        return array_fill(0, $nbrOfRuleSetsInArray, $this->createMock(RuleSet::class));
    }
}
