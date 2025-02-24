<?php

namespace Municipio\ExternalContent\SourceReaders;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\FileSystem\FileExists;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceReaderTest extends TestCase {
    
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated() {
        $jsonFileSourceReader = new JsonFileSourceReader('', $this->getGetFileContentMock(), $this->getFileExistsMock(), $this->getJsonToSchemaObjectsMock());
        $this->assertInstanceOf(JsonFileSourceReader::class, $jsonFileSourceReader);
    }

    /**
     * @testdox getSourceData() returns an array
     */
    public function testGetSourceDataReturnsArrayOfSchemaObjects() {
        $fileExists = $this->getFileExistsMock();
        $fileExists->method('fileExists')->willReturn(true);
        $jsonFileSourceReader = new JsonFileSourceReader('', $this->getGetFileContentMock(), $fileExists, $this->getJsonToSchemaObjectsMock());
        $this->assertIsArray($jsonFileSourceReader->getSourceData());
    }

    /**
     * @testdox getSourceData() transforms json to schema objects
     */
    public function testGetSourceDataReturnsArrayOfSchemaObjectsFoundInJsonFile() {
        $fileExists = $this->getFileExistsMock();
        $fileExists->method('fileExists')->willReturn(true);
        $json = '[{ "@context": "https://schema.org", "@type": "Organization", "name": "Helsingborgs stad"}]';
        $getFileContent = $this->getGetFileContentMock();
        $jsonToSchemaObjects = $this->getJsonToSchemaObjectsMock();
        
        $getFileContent->expects($this->once())->method('getFileContent')->willReturn($json);
        $jsonToSchemaObjects->method('transform')->with($json)->willReturn([]);

        $jsonFileSourceReader = new JsonFileSourceReader('', $getFileContent, $fileExists, $jsonToSchemaObjects);
        $jsonFileSourceReader->getSourceData();
    }

    /**
     * @testdox getSourceData() throws if file does not exist
     */
    public function testGetSourceDataThrowsIfFileDoesNotExist() {
        $fileExists = $this->getFileExistsMock();
        $fileExists->method('fileExists')->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);
        $jsonFileSourceReader = new JsonFileSourceReader('invalid filePath', $this->getGetFileContentMock(), $fileExists, $this->getJsonToSchemaObjectsMock());
        $jsonFileSourceReader->getSourceData();
    }

    private function getJsonToSchemaObjectsMock(): JsonToSchemaObjects|MockObject {
        return $this->createMock(JsonToSchemaObjects::class);
    }

    private function getGetFileContentMock(): GetFileContent|MockObject {
        return $this->createMock(GetFileContent::class);
    }

    private function getFileExistsMock(): FileExists|MockObject {
        return $this->createMock(FileExists::class);
    }
}