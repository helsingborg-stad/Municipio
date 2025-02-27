<?php

namespace Municipio\ExternalContent\SourceReaders\Factories;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\ExternalContent\SourceReaders\JsonFileSourceReader;
use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\FileSystem\FileSystem;

class SourceReaderFromConfigTest extends TestCase
{
    private SourceReaderFromConfig $sourceReaderFromConfig;

    protected function setUp(): void
    {
        $this->sourceReaderFromConfig = new SourceReaderFromConfig();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(SourceReaderFromConfig::class, $this->sourceReaderFromConfig);
    }

    /**
     * @testdox create() returns an implementation of SourceReaderInterface
     */
    public function testCreateReturnsSourceReaderInterface()
    {
        $sourceConfig = $this->getSourceConfigMock();
        $this->assertInstanceOf(SourceReaderInterface::class, $this->sourceReaderFromConfig->create($sourceConfig));
    }

    /**
     * @testdox create() returns a JsonFileSourceReader when the config
     */
    public function testCreateReturnsJsonFileSourceReader()
    {
        $sourceConfig = $this->getSourceConfigMock();
        $sourceConfig->method('getSourceType')->willReturn('json');

        $this->assertInstanceOf(JsonFileSourceReader::class, $this->sourceReaderFromConfig->create($sourceConfig));
    }

    private function getSourceConfigMock(): SourceConfigInterface|MockObject
    {
        return $this->createMock(SourceConfigInterface::class);
    }
}
