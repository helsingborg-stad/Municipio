<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

use PHPUnit\Framework\TestCase;

class StringFieldTest extends TestCase
{
    public function testFieldContainsTextType()
    {
        $field   = new StringField();
        $created = $field->create('schemaType', 'propertyName', ['string']);

        $this->assertEquals('text', $created['type']);
    }
}
