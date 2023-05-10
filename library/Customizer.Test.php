<?php

class CustomizerTest extends Phpunit\Framework\TestCase
{
    public function testSanitizeKirkiDefaultArrayValueConvertsEmptyStringValueToArrayIfDefaultIsArray()
    {
        $value = '';
        $default = ['foo' => 'bar'];
        $customizer = new \Municipio\Customizer();

        $sanitizedValue = $customizer->sanitizeKirkiDefaultArrayValue($value, $default);

        $this->assertEquals(['foo' => 'bar'], $sanitizedValue);
    }
}