<?php

namespace Municipio\SchemaData\ExternalContent\Config;

use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\FilterDefinition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SourceConfigWithCustomFilterDefinitionTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $sourceConfig = new SourceConfigWithCustomFilterDefinition($this->getFilterDefinition(), $this->getSourceConfig());
        $this->assertInstanceOf(SourceConfigWithCustomFilterDefinition::class, $sourceConfig);
    }

    /**
     * @testdox getFilterDefinition() returns provided filterDefinition
     */
    public function testGetFilterDefinitionReturnsFilterDefinition()
    {
        $filterDefinition = $this->getFilterDefinition();
        $sourceConfig     = new SourceConfigWithCustomFilterDefinition($filterDefinition, $this->getSourceConfig());
        $this->assertEquals($filterDefinition, $sourceConfig->getFilterDefinition());
    }

    private function getFilterDefinition(): FilterDefinition|MockObject
    {
        return $this->createMock(FilterDefinition::class);
    }

    private function getSourceConfig(): SourceConfigInterface|MockObject
    {
        return $this->createMock(SourceConfigInterface::class);
    }
}
