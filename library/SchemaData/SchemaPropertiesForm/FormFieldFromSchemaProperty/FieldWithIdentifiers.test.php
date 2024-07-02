<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

use PHPUnit\Framework\TestCase;

class FieldWithIdentifiersTest extends TestCase
{
    public function testFieldContainsIdentifiersUsingPropertyName()
    {
        $field   = new FieldWithIdentifiers();
        $created = $field->create('schemaType', 'propertyName', ['string']);

        $this->assertEquals('schema_propertyName', $created['key']);
        $this->assertEquals('propertyName', $created['label']);
        $this->assertEquals('schema_propertyName', $created['name']);
    }
}
