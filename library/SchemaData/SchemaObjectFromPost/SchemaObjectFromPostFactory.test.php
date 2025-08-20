<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizerInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SchemaObjectFromPostFactoryTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(SchemaObjectFromPostFactory::class, $this->getSUT());
    }

    /**
     * @testdox create() returns an instance of SchemaObjectFromPostInterface
     */
    public function testCreateReturnsInstanceOfSchemaObjectFromPostInterface()
    {
        $this->assertInstanceOf(SchemaObjectFromPostInterface::class, $this->getSUT()->create());
    }

    private function getSUT(): SchemaObjectFromPostFactory
    {
        return new SchemaObjectFromPostFactory(
            $this->createMock(TryGetSchemaTypeFromPostType::class),
            new FakeWpService(),
            $this->createMock(GetSchemaPropertiesWithParamTypesInterface::class),
            $this->createMock(SchemaPropertyValueSanitizerInterface::class),
        );
    }
}
