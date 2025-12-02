<?php

namespace Municipio\SchemaData\ExternalContent\Config;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SourceConfigWithUniqueIdTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $sourceConfig = new SourceConfigWithUniqueId($this->getInnerSourceConfigMock());
        $this->assertInstanceOf(SourceConfigWithUniqueId::class, $sourceConfig);
    }

    #[TestDox('getId() always returns unique id')]
    public function testGetId()
    {
        $innerSourceConfig = $this->getInnerSourceConfigMock();
        $innerSourceConfig->method('getId')->willReturn('test-id');

        $sourceConfigOne   = new SourceConfigWithUniqueId($innerSourceConfig);
        $sourceConfigTwo   = new SourceConfigWithUniqueId($innerSourceConfig);
        $sourceConfigThree = new SourceConfigWithUniqueId($innerSourceConfig);

        $this->assertNotSame($sourceConfigOne->getId(), $sourceConfigTwo->getId());
        $this->assertNotSame($sourceConfigOne->getId(), $sourceConfigThree->getId());
        $this->assertNotSame($sourceConfigTwo->getId(), $sourceConfigThree->getId());
    }

    #[TestDox('getId() returns same id for same instance')]
    public function testGetIdReturnsSameIdForSameInstance()
    {
        $innerSourceConfig = $this->getInnerSourceConfigMock();
        $innerSourceConfig->method('getId')->willReturn('test-id');

        $sourceConfig = new SourceConfigWithUniqueId($innerSourceConfig);
        $this->assertSame($sourceConfig->getId(), $sourceConfig->getId());
    }

    private function getInnerSourceConfigMock(): SourceConfigInterface|MockObject
    {
        return $this->createMock(SourceConfigInterface::class);
    }
}
