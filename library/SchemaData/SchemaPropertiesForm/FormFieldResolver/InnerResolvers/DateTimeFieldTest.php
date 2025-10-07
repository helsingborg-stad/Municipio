<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use PHPUnit\Framework\TestCase;

class DateTimeFieldTest extends TestCase
{
    public function testFieldContainsTextType()
    {
        $field = new DateTimeField(['\DateTimeInterface'], new EmptyField());
        $this->assertEquals('date_time_picker', $field->resolve()['type']);
    }
}
