<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class BooleanSanitizerTest extends TestCase
{
    public function testAcceptsBoolean()
    {
        $sanitizer = new BooleanSanitizer();
        $this->assertTrue($sanitizer->sanitize(true, ['bool']));
        $this->assertFalse($sanitizer->sanitize(false, ['bool']));
    }

    public function testSanitizesBooleanAsString()
    {
        $sanitizer = new BooleanSanitizer();
        $this->assertTrue($sanitizer->sanitize('true', ['bool']));
        $this->assertFalse($sanitizer->sanitize('false', ['bool']));
    }

    public function testSanitizesInteger()
    {
        $sanitizer = new BooleanSanitizer();
        $this->assertTrue($sanitizer->sanitize(1, ['bool']));
        $this->assertFalse($sanitizer->sanitize(0, ['bool']));
    }

    public function testSanitizesArray()
    {
        $sanitizer = new BooleanSanitizer();
        $this->assertEquals([true, false], $sanitizer->sanitize(['true', 'false'], ['bool']));
    }
}
