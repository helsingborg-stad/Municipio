<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Config\JsonFileSourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use PHPUnit\Framework\TestCase;
use WpService\FileSystem\GetFileContent;

class JsonFileSourceServiceDecoratorTest extends TestCase {
    
    /**
     * @testdox getObject() returns ThingContract on success
     */
    public function testGetObjectSuccess() {
        $fileContent = '[
            {
                "@type": "Thing",
                "@id": 123
            }
        ]';

        $fileSystem = $this->getFileSystem($fileContent);
        $jsonToSchemaObjects = new SimpleJsonConverter();
        $config = $this->getConfig(['file' => 'fileLocation']);
        $service = new JsonFileSourceServiceDecorator($config, $fileSystem, $jsonToSchemaObjects, new Source('', ''));
        
        $thing = $service->getObject('123');

        $this->assertEquals('123', $thing->getProperty('@id'));
    }

    /**
     * @testdox getObject() returns null on failure
     */
    public function testGetObjectFailure() {
        $fileContent = '[
            {
                "@type": "Thing",
                "@id": 123
            }
        ]';

        $fileSystem = $this->getFileSystem($fileContent);
        $jsonToSchemaObjects = new SimpleJsonConverter();
        $config = $this->getConfig(['file' => 'fileLocation']);
        $service = new JsonFileSourceServiceDecorator($config, $fileSystem, $jsonToSchemaObjects, new Source('', ''));
        
        $thing = $service->getObject('456');

        $this->assertNull($thing);
    }

    /**
     * @testdox getObjects() returns all objects in file
     */
    public function testGetObjects() {
        $fileContent = '[
            {
                "@type": "Thing",
                "@id": 123
            },
            {
                "@type": "Thing",
                "@id": 456
            }
        ]';

        $fileSystem = $this->getFileSystem($fileContent);
        $jsonToSchemaObjects = new SimpleJsonConverter();
        $config = $this->getConfig(['file' => 'fileLocation']);
        $service = new JsonFileSourceServiceDecorator($config, $fileSystem, $jsonToSchemaObjects, new Source('', ''));
        
        $things = $service->getObjects(null);

        $this->assertCount(2, $things);
        $this->assertEquals('123', $things[0]->getProperty('@id'));
        $this->assertEquals('456', $things[1]->getProperty('@id'));
    }

    private function getFileSystem(string|false $fileContent): GetFileContent {
        return new class ($fileContent) implements GetFileContent {
            
            public function __construct(private string|false $fileContent)
            {
            }

            public function getFileContent(string $path): string|false
            {
                return $this->fileContent;
            }


        };
    }

    private function getConfig(array $values): JsonFileSourceConfigInterface {
        return new class ($values) implements JsonFileSourceConfigInterface {
            
            public function __construct(private array $values)
            {
            }

            public function getFile(): string
            {
                return $this->values['file'];
            }

            public function getPostType(): string
            {
                return $this->values['postType'];
            }

            public function getSchemaObjectType(): string
            {
                return $this->values['schemaObjectType'] ?? '';
            }
        };
    }
}