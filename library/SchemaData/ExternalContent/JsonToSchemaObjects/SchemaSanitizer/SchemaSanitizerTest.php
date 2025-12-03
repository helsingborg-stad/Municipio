<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer;

use DateTimeInterface;
use Municipio\Schema\Schema;
use Municipio\Schema\Thing;
use Municipio\Schema\Type;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes;
use PHPUnit\Framework\TestCase;

class SchemaSanitizerTest extends TestCase
{
    #[TestDox('returns the same schema object that was passed in')]
    public function testReturnsTheSameSchemaObjectThatWasPassedIn(): void
    {
        $schema    = Schema::thing();
        $sanitizer = new SchemaSanitizer(new SchemaPropertyValueSanitizer(), new GetSchemaPropertiesWithParamTypes());
        $this->assertInstanceOf(Thing::class, $sanitizer->sanitize($schema));
    }

    #[TestDox('converts string date to DateTime object')]
    public function testConvertsStringDateToDateTimeObject(): void
    {
        $schema    = Schema::event()->startDate('2024-01-01T12:00:00Z');
        $sanitizer = new SchemaSanitizer(new SchemaPropertyValueSanitizer(), new GetSchemaPropertiesWithParamTypes());

        $this->assertInstanceOf(DateTimeInterface::class, $sanitizer->sanitize($schema)->getProperty('startDate'));
        $this->assertEquals('2024-01-01T12:00:00+00:00', $sanitizer->sanitize($schema)->getProperty('startDate')->format(DateTimeInterface::RFC3339));
    }
}
