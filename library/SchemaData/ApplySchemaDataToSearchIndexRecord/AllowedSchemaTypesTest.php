<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;


use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AllowedSchemaTypesTest extends TestCase
{
    #[TestDox('allowed types are an array of strings')]
    public function testIsArray(): void
    {
        $sut = new AllowedSchemaTypes();

        static::assertIsArray($sut->getAllowedSchemaTypes());
        foreach ($sut->getAllowedSchemaTypes() as $element) {
            if (is_string($element)) { continue; }

static::fail('element was not a string');
        }

        static::assertTrue(true);
    }

    #[TestDox('isAllowed returns false for non allowed')]
    public function testNonAllowed(): void
    {
        $sut = new AllowedSchemaTypes();
        static::assertFalse($sut->isAllowed('SomeRandomSchemaTypeThatDoesNotExist'));
    }

    #[TestDox('isAllowed returns true for allowed')]
    public function testAllowed(): void
    {
        $sut = new AllowedSchemaTypes();
        static::assertTrue($sut->isAllowed('Event'));
    }
}
