<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use PHPUnit\Framework\TestCase;

class FieldWithIdentifiersTest extends TestCase
{
    public function testFieldContainsIdentifiersUsingPropertyName()
    {
        $field    = new FieldWithIdentifiers('propertyName', new EmptyField());
        $resolved = $field->resolve();

        $this->assertEquals('schema_propertyName', $resolved['key']);
        $this->assertEquals('propertyName', $resolved['label']);
        $this->assertEquals('schema_propertyName', $resolved['name']);
    }
}
