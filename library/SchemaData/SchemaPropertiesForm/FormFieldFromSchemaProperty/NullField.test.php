<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldFromSchemaProperty;

use PHPUnit\Framework\TestCase;

class NullFieldTest extends TestCase
{
    public function testReturnsEmptyArrayIfAcceptedPropertyTypesDoesNotContainNull()
    {
        $field  = new NullField();
        $result = $field->create('schemaType', 'propertyName', ['string']);
        $this->assertNull($result['type']);
    }
}
