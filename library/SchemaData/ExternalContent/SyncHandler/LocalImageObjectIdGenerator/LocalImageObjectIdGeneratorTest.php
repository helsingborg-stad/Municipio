<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class LocalImageObjectIdGeneratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(LocalImageObjectIdGeneratorInterface::class, new LocalImageObjectIdGenerator());
    }

    /**
     * @testdox generates id from schema object id and image object url.
     */
    public function testGeneratesIdFromSchemaObjectIdAndImageObjectUrl(): void
    {
        $schemaObject = Schema::thing()->setProperty('@id', 'schema-object-id');
        $imageObject  = Schema::imageObject()->url('https://example.com/image.jpg');

        $generator = new LocalImageObjectIdGenerator();
        $id        = $generator->generateId($schemaObject, $imageObject);

        $this->assertEquals('schema-object-id-https://example.com/image.jpg', $id);
    }
}
