<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use PHPUnit\Framework\TestCase;

class StringFieldTest extends TestCase
{
    public function testFieldContainsTextType()
    {
        $field = new StringField(['string'], new EmptyField());

        $this->assertEquals('text', $field->resolve()['type']);
    }
}
