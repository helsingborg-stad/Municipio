<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\SchemaSanitizerInterface;
use PHPUnit\Framework\TestCase;

class JsonConverterWithSanitizedPropertiesTest extends TestCase {
    /**
     * @testdox applies sanitizer to result of inner converter
     */
    public function testAppliesSanitizerToResultOfInnerConverter(): void {
        $inner = new class implements JsonToSchemaObjectsInterface {
            public function transform(string $json): array
            {
                return [Schema::thing()->name('Original name')];
            }
        };
        $sanitizer = new class implements SchemaSanitizerInterface {
            public function sanitize(BaseType $object): BaseType
            {
                $object->name('Sanitized name');
                return $object;
            }
        };
        $converter = new JsonConverterWithSanitizedProperties($sanitizer, $inner);
        $converter->transform('[]');

        $this->assertSame('Sanitized name', $converter->transform('[]')[0]->getProperty('name'));
    }
}