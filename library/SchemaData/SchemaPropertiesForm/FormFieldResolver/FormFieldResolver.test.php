<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver;

use PHPUnit\Framework\TestCase;

class FormFieldResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $resolver = new FormFieldResolver([], 'propertyName');
        $this->assertInstanceOf(FormFieldResolver::class, $resolver);
    }

    /**
     * @testdox resolve() returns an array
     */
    public function testResolveReturnsArray()
    {
        $resolver = new FormFieldResolver([], 'propertyName');
        $result   = $resolver->resolve();
        $this->assertIsArray($result);
    }
}
