<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class StringSanitizerTest extends TestCase
{
    public function testSanitizeString()
    {
        $sanitizer = new StringSanitizer();
        $this->assertEquals('value', $sanitizer->sanitize('value', ['string']));
    }

    public function testReturnsNullIfNotString()
    {
        $sanitizer = new StringSanitizer();
        $this->assertNull($sanitizer->sanitize([], ['string']));
    }
}
