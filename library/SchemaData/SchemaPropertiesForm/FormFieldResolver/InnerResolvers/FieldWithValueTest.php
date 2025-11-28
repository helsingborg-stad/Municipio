<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use AcfService\Implementations\FakeAcfService;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FieldWithValue
 *
 * This class is responsible for resolving the form field properties for a given field with a value.
 */
class FieldWithValueTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(FieldWithValue::class, new FieldWithValue(new FakeAcfService(), 'property_name', $this->getInner()));
    }

    #[TestDox('resolve() returns array with value')]
    public function testResolveReturnsArrayWithValueAndInnerProperties()
    {
        $inner             = $this->getInner();
        $expectedFieldName = FieldWithIdentifiers::FIELD_PREFIX . 'property_name';
        $acfService        = new FakeAcfService(['getField' => fn($fieldName) => $fieldName === $expectedFieldName ? 'value' : null]);

        $resolver          = new FieldWithValue($acfService, 'property_name', $inner);

        $this->assertEquals([ 'value' => 'value', ], $resolver->resolve());
    }

    #[TestDox('resolve() returns array with inner properties')]
    public function testResolveReturnsArrayWithInnerProperties()
    {
        $inner = $this->getInner();
        $inner->method('resolve')->willReturn(['innerProperty' => 'innerValue']);
        $acfService = new FakeAcfService();

        $resolver = new FieldWithValue($acfService, 'property_name', $inner);

        $this->assertEquals('innerValue', $resolver->resolve()['innerProperty']);
    }

    private function getInner(): FormFieldResolverInterface|MockObject
    {
        return $this->createMock(FormFieldResolverInterface::class);
    }
}
