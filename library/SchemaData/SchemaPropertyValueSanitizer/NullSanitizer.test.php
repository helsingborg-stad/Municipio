<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class NullSanitizerTest extends TestCase
{
    public function testReturnsNull(): void
    {
        $sanitizer = new NullSanitizer();
        $this->assertNull($sanitizer->sanitize('value', []));
    }
}
