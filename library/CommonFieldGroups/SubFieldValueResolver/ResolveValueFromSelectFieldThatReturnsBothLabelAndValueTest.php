<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetOption;

class ResolveValueFromSelectFieldThatReturnsBothLabelAndValueTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanInstantiate(): void
    {
        $resolver = new ResolveValueFromSelectFieldThatReturnsBothLabelAndValue($this->createGetOptionMock());
        $this->assertInstanceOf(ResolveValueFromSelectFieldThatReturnsBothLabelAndValue::class, $resolver);
    }

    #[TestDox('resolves both label and value if is select field with array return')]
    public function testResolvesBothLabelAndValue(): void
    {
        $fieldChoices  = ['choiceOne' => 'Label for Choice One'];
        $field         = ['type' => 'select', 'return_format' => 'array', 'choices' => $fieldChoices];
        $fieldValue    = 'choiceOne';
        $fieldKey      = 'test_field';
        $getOptionMock = $this->createGetOptionMock();
        $getOptionMock->method('getOption')->with($fieldKey)->willReturn($fieldValue);

        $resolver = new ResolveValueFromSelectFieldThatReturnsBothLabelAndValue($getOptionMock);
        $result   = $resolver->resolve($field, $fieldKey);

        $this->assertEquals(['label' => 'Label for Choice One', 'value' => 'choiceOne'], $result);
    }

    private function createGetOptionMock(): GetOption|MockObject
    {
        return $this->createMock(GetOption::class);
    }
}
