<?php

namespace Municipio\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @group wp_mock
 */
class CustomizerTest extends TestCase
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