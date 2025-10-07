<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

use PHPUnit\Framework\TestCase;

class JsonToSchemaObjectsFactoryTest extends TestCase {
    #[TestDox('returns an instance of JsonToSchemaObjectsInterface')]
    public function testCreate() {
        $factory = new JsonToSchemaObjectsFactory();
        $instance = $factory->create();
        $this->assertInstanceOf(JsonToSchemaObjectsInterface::class, $instance);
    }
}