<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapIcsUrlTest extends TestCase
{
    /**
     * @testdox returns calendar url if startDatee, endDate and name are present
     */
    public function testReturnsCalendarUrlIfStartDateEndDateAndNameArePresent()
    {
        $event = Schema::event()
            ->startDate(new DateTime('2025-06-01 10:00:00'))
            ->endDate(new DateTime('2025-06-01 12:00:00'))
            ->name('Sample Event');

        $mapper = new MapIcsUrl();
        $icsUrl = $mapper->map($event);

        // Assert that the returned URL is a valid calendar URL
        $this->assertStringStartsWith('data:text/calendar;charset=utf8,', $icsUrl);
        $this->assertStringContainsString('BEGIN:VCALENDAR', $icsUrl);
        $this->assertStringContainsString('SUMMARY:Sample Event', $icsUrl);
        $this->assertStringContainsString('DTSTART:20250601T100000Z', $icsUrl);
        $this->assertStringContainsString('DTEND:20250601T120000Z', $icsUrl);
    }

    /**
     * @testdox returns null if startDate is missing
     */
    public function testReturnsEmptyStringIfStartDateIsMissing()
    {
        $event = Schema::event()
            ->endDate(new DateTime('2025-06-01 12:00:00'))
            ->name('Sample Event');

        $mapper = new MapIcsUrl();

        $this->assertNull($mapper->map($event));
    }

    /**
     * @testdox returns null if endDate is missing
     */
    public function testReturnsEmptyStringIfEndDateIsMissing()
    {
        $event = Schema::event()
            ->startDate(new DateTime('2025-06-01 10:00:00'))
            ->name('Sample Event');

        $mapper = new MapIcsUrl();

        $this->assertNull($mapper->map($event));
    }

    /**
     * @testdox returns null if name is missing
     */
    public function testReturnsEmptyStringIfNameIsMissing()
    {
        $event = Schema::event()
            ->startDate(new DateTime('2025-06-01 10:00:00'))
            ->endDate(new DateTime('2025-06-01 12:00:00'));

        $mapper = new MapIcsUrl();

        $this->assertNull($mapper->map($event));
    }
}
