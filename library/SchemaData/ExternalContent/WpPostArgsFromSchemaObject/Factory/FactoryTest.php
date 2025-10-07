<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\Factory;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Municipio\Helper\WpService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FactoryTest extends TestCase
{
    protected function setUp(): void
    {
        WpService::set(new FakeWpService());
        parent::setUp();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $factory = new Factory($this->getSourceConfig(), new FakeWpService());
        $this->assertInstanceOf(Factory::class, $factory);
    }

    /**
     * @testdox create() returns a WpPostArgsFromSchemaObjectInterface
     */
    public function testCreateReturnsAWpPostArgsFromSchemaObjectInterface()
    {
        $factory = new Factory($this->getSourceConfig(), new FakeWpService());
        $this->assertInstanceOf(WpPostArgsFromSchemaObjectInterface::class, $factory->create());
    }

    private function getSourceConfig(): SourceConfigInterface|MockObject
    {
        return $this->createMock(SourceConfigInterface::class);
    }
}
