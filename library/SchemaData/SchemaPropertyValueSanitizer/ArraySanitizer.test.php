<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class ArraySanitizerTest extends TestCase
{
    public function testReturnsArrayOfValues()
    {
        $sanitizer = new ArraySanitizer(new StringSanitizer());
        $this->assertEquals(['foo', 'bar'], $sanitizer->sanitize(['foo', 'bar'], ['string[]']));
    }
}
