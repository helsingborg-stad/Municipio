<?php

namespace Municipio\ExternalContent\PropertyPathFilterDefinition;

use Municipio\ExternalContent\PropertyPathFilterDefinition\Contracts\RuleSetInterface;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $filter = new Filter($this->getArrayOfRuleSets());
        $this->assertInstanceOf(Filter::class, $filter);
    }

    /**
     * @testdox class can not be instantiated with empty ruleSets
     */
    public function testCanBeInstantiatedWithEmptyRuleSets()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Filter([]);
    }

    /**
     * @testdox getRuleSets() returns provided ruleSets
     */
    public function testGetRuleSetsReturnsRuleSets()
    {
        $ruleSets = $this->getArrayOfRuleSets(2);
        $filter   = new Filter($ruleSets);
        $this->assertCount(2, $filter->getRuleSets());
    }

    private function getArrayOfRuleSets(int $nbrOfRuleSetsInArray = 1): array
    {
        return array_fill(0, $nbrOfRuleSetsInArray, $this->createMock(RuleSetInterface::class));
    }
}
