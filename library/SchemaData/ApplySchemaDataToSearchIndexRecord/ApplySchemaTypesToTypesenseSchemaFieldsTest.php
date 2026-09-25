<?php

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ApplySchemaTypesToTypesenseSchemaFieldsTest extends TestCase
{
    #[TestDox('attaches filter to existing hook')]
    public function testAddHooks(): void
    {
        $sut = new ApplySchemaTypesToTypesenseSchemaFields(static::createWpService());
        $sut->addHooks();
        static::assertTrue(true, 'filter hook does not cause error');
    }

    #[TestDox('expected schema types are added to the typesense schema')]
    public function testExpectedSchemaTypes(): void
    {
        $sut = new ApplySchemaTypesToTypesenseSchemaFields(static::createWpService());
        $fields = [];

        foreach($sut->apply($fields) as $schemaField) {
            static::assertSame('object', $schemaField['type']);
            static::assertTrue($schemaField['optional']);
        }
    }

    private static function createWpService(): AddFilter
    {
        return new class implements AddFilter {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }
        };
    }
}
