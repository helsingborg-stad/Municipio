<?php

namespace Municipio\Theme;

use Municipio\SchemaData\Utils\Contracts\SchemaTypesInUseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $navigation = new Navigation($this->getSchemaTypesInUse());
        $this->assertInstanceOf(Navigation::class, $navigation);
    }

    #[TestDox('getSchemaTypeMenus() returns an array of menu locations for schema types in use')]
    public function testGetSchemaTypeMenusReturnsArrayOfMenuLocations()
    {
        $schemaTypesInUse = $this->getSchemaTypesInUse();
        $schemaTypesInUse->method('getSchemaTypesInUse')->willReturn(['Event']);
        $navigation      = new Navigation($schemaTypesInUse);
        $schemaTypeMenus = $navigation->getSchemaTypeMenus();

        $this->assertArrayHasKey('event-secondary-menu', $schemaTypeMenus, 'Array does not contain expected key');
    }

    #[TestDox('getMenuLocations() returns an empty array if no schema types are in use')]
    public function testGetMenuLocationsReturnsEmptyArrayIfNoSchemaTypesInUse()
    {
        $schemaTypesInUse = $this->getSchemaTypesInUse();
        $schemaTypesInUse->method('getSchemaTypesInUse')->willReturn([]);
        $navigation      = new Navigation($schemaTypesInUse);
        $schemaTypeMenus = $navigation->getSchemaTypeMenus();

        $this->assertEmpty($schemaTypeMenus, 'Array is not empty when it should be');
    }

    private function getSchemaTypesInUse(): SchemaTypesInUseInterface|MockObject
    {
        return $this->createMock(SchemaTypesInUseInterface::class);
    }
}
