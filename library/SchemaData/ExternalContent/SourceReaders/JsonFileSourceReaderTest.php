<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use Municipio\SchemaData\ExternalContent\Filter\SchemaObjectsFilter\SchemaObjectsFilterInterface;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjectsInterface;
use Municipio\SchemaData\ExternalContent\SourceReaders\FileSystem\FileSystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsonFileSourceReaderTest extends TestCase {
    
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated() {
        $jsonFileSourceReader = new JsonFileSourceReader('', $this->getSchemaObjectsFilterMock(), $this->getFileSystemMock(), $this->getJsonToSchemaObjectsMock());
        $this->assertInstanceOf(JsonFileSourceReader::class, $jsonFileSourceReader);
    }

    /**
     * @testdox getSourceData() returns an array
     */
    public function testGetSourceDataReturnsArrayOfSchemaObjects() {
        $fileSystem = $this->getFileSystemMock();
        $fileSystem->method('fileExists')->willReturn(true);
        $jsonFileSourceReader = new JsonFileSourceReader('', $this->getSchemaObjectsFilterMock(), $fileSystem, $this->getJsonToSchemaObjectsMock());
        $this->assertIsArray($jsonFileSourceReader->getSourceData());
    }

    /**
     * @testdox getSourceData() transforms json to schema objects
     */
    public function testGetSourceDataReturnsArrayOfSchemaObjectsFoundInJsonFile() {
        $fileSystem = $this->getFileSystemMock();
        $fileSystem->method('fileExists')->willReturn(true);
        $json = '[{ "@context": "https://schema.org", "@type": "Organization", "name": "Helsingborgs stad"}]';
        $jsonToSchemaObjects = $this->getJsonToSchemaObjectsMock();
        
        $fileSystem->expects($this->once())->method('fileGetContents')->willReturn($json);
        $jsonToSchemaObjects->method('transform')->with($json)->willReturn([]);

        $jsonFileSourceReader = new JsonFileSourceReader('', $this->getSchemaObjectsFilterMock(), $fileSystem, $jsonToSchemaObjects);
        $jsonFileSourceReader->getSourceData();
    }

    /**
     * @testdox getSourceData() throws if file does not exist
     */
    public function testGetSourceDataThrowsIfFileDoesNotExist() {
        $fileSystem = $this->getFileSystemMock();
        $fileSystem->method('fileExists')->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);
        $jsonFileSourceReader = new JsonFileSourceReader('invalid filePath', $this->getSchemaObjectsFilterMock(), $fileSystem, $this->getJsonToSchemaObjectsMock());
        $jsonFileSourceReader->getSourceData();
    }

    /**
     * @testdox getSourceData() returns filtered schema objects
     */
    public function testGetSourceDataFiltersSchemaObjects() {
        $fileSystem = $this->getFileSystemMock();
        $fileSystem->method('fileExists')->willReturn(true);
        $json = '[{ "@context": "https://schema.org", "@type": "Organization", "name": "Helsingborgs stad"}]';
        $jsonToSchemaObjects = $this->getJsonToSchemaObjectsMock();
        $schemaObjectsFilter = $this->getSchemaObjectsFilterMock();
        
        $fileSystem->method('fileGetContents')->willReturn($json);
        $jsonToSchemaObjects->method('transform')->with($json)->willReturn([new \Municipio\Schema\Schema()]);

        $schemaObjectsFilter->expects($this->once())->method('filter')->willReturn(['filteredResults']);
        $jsonFileSourceReader = new JsonFileSourceReader('', $schemaObjectsFilter, $fileSystem, $jsonToSchemaObjects);
        
        $filteredSchemaObjects = $jsonFileSourceReader->getSourceData();

        $this->assertIsArray($filteredSchemaObjects);
        $this->assertEquals(['filteredResults'], $filteredSchemaObjects);
    }

    private function getJsonToSchemaObjectsMock(): JsonToSchemaObjectsInterface|MockObject {
        return $this->createMock(JsonToSchemaObjectsInterface::class);
    }

    private function getFileSystemMock(): FileSystem|MockObject {
        return $this->createMock(FileSystem::class);
    }

    private function getSchemaObjectsFilterMock(): SchemaObjectsFilterInterface|MockObject {
        return $this->createMock(SchemaObjectsFilterInterface::class);
    }
}