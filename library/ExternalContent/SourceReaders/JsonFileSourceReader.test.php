<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\FileSystem\FileSystem;

class JsonFileSourceReaderTest extends TestCase {
    
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated() {
        $jsonFileSourceReader = new JsonFileSourceReader('', $this->getFileSystemMock(), $this->getJsonToSchemaObjectsMock());
        $this->assertInstanceOf(JsonFileSourceReader::class, $jsonFileSourceReader);
    }

    /**
     * @testdox getSourceData() returns an array
     */
    public function testGetSourceDataReturnsArrayOfSchemaObjects() {
        $fileSystem = $this->getFileSystemMock();
        $fileSystem->method('fileExists')->willReturn(true);
        $jsonFileSourceReader = new JsonFileSourceReader('', $fileSystem, $this->getJsonToSchemaObjectsMock());
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
        
        $fileSystem->expects($this->once())->method('getFileContent')->willReturn($json);
        $jsonToSchemaObjects->method('transform')->with($json)->willReturn([]);

        $jsonFileSourceReader = new JsonFileSourceReader('', $fileSystem, $jsonToSchemaObjects);
        $jsonFileSourceReader->getSourceData();
    }

    /**
     * @testdox getSourceData() throws if file does not exist
     */
    public function testGetSourceDataThrowsIfFileDoesNotExist() {
        $fileSystem = $this->getFileSystemMock();
        $fileSystem->method('fileExists')->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);
        $jsonFileSourceReader = new JsonFileSourceReader('invalid filePath', $fileSystem, $this->getJsonToSchemaObjectsMock());
        $jsonFileSourceReader->getSourceData();
    }

    private function getJsonToSchemaObjectsMock(): JsonToSchemaObjects|MockObject {
        return $this->createMock(JsonToSchemaObjects::class);
    }

    private function getFileSystemMock(): FileSystem|MockObject {
        return $this->createMock(FileSystem::class);
    }
}