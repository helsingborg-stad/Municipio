<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;
use PHPUnit\Framework\TestCase;

class EmptyFieldTest extends TestCase
{
    #[TestDox('resolve() returns an empty array')]
    public function testResolveReturnsEmptyArray()
    {
        $emptyField = new EmptyField();
        $this->assertEquals([], $emptyField->resolve());
    }
}
