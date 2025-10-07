<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class NullSanitizerTest extends TestCase
{
    #[TestDox('returns the same value as input')]
    public function testSanitizeReturnsSameValue()
    {
        $sanitizer = new NullSanitizer();
        $this->assertEquals('value', $sanitizer->sanitize('value', []));
    }
}
