<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use PHPUnit\Framework\TestCase;

class DateTimeSanitizerTest extends TestCase
{
    public function testReturnsDateAsString()
    {
        $sanitizer = new DateTimeSanitizer();
        $sanitized = $sanitizer->sanitize('2021-01-01', ['string']);
        $this->assertEquals('2021-01-01', $sanitized);
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
        $sanitized = $sanitizer->sanitize(['2021-01-01'], ['\DateTimeInterface[]']);

        $this->assertEquals('2021-01-01', $sanitized[0]->format('Y-m-d'));
    }
}
