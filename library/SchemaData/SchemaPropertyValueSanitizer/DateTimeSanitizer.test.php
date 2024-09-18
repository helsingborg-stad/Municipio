<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class DateTimeSanitizerTest extends TestCase
{
    public function testReturnsDateAsString()
    {
        $sanitizer = new DateTimeSanitizer();
        $this->assertEquals('2021-01-01', $sanitizer->sanitize('2021-01-01', ['\DateTimeInterface']));
    }

    public function testReturnsDateTimeAsDateTime()
    {
        $sanitizer = new DateTimeSanitizer();
        $dateTime  = $sanitizer->sanitize('2021-01-01 12:00', ['\DateTimeInterface']);
        $this->assertInstanceOf(\DateTime::class, $dateTime);
    }

    public function testReturnsArrayOfDates()
    {
        $sanitizer = new DateTimeSanitizer();
        $this->assertEquals(['2021-01-01'], $sanitizer->sanitize(['2021-01-01'], ['\DateTimeInterface[]']));
    }
}
