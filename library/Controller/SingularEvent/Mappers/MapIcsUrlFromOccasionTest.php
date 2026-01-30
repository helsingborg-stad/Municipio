<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapIcsUrlFromOccasionTest extends TestCase
{
    #[TestDox('returns calendar url if current occasion provided and event name is present')]
    public function testReturnsCalendarUrlIfStartDateEndDateAndNameArePresent()
    {
        $event = Schema::event()->name('Sample Event');
        $occasion = $this->getFakeOccasion('2025-06-01 10:00:00', '2025-06-01 12:00:00');
        $mapper = new MapIcsUrlFromOccasion($occasion);

        $icsUrl = $mapper->map($event);

        // Assert that the returned URL is a valid calendar URL
        $this->assertStringStartsWith('data:text/calendar;charset=utf8,', $icsUrl);
        $this->assertStringContainsString('BEGIN:VCALENDAR', $icsUrl);
        $this->assertStringContainsString('SUMMARY:Sample Event', $icsUrl);
        $this->assertStringContainsString('DTSTART:20250601T100000Z', $icsUrl);
        $this->assertStringContainsString('DTEND:20250601T120000Z', $icsUrl);
    }

    #[TestDox('returns null if provided occasion is null')]
    public function testReturnsEmptyStringIfStartDateIsMissing()
    {
        $event = Schema::event()->name('Sample Event');
        $mapper = new MapIcsUrlFromOccasion();

        $this->assertNull($mapper->map($event));
    }

    #[TestDox('returns null if name is missing')]
    public function testReturnsEmptyStringIfNameIsMissing()
    {
        $event = Schema::event();
        $occasion = $this->getFakeOccasion('2025-06-01 10:00:00', '2025-06-01 12:00:00');
        $mapper = new MapIcsUrlFromOccasion($occasion);

        $this->assertNull($mapper->map($event));
    }

    private function getFakeOccasion(string $startDate, string $endDate): OccasionInterface
    {
        return new class($startDate, $endDate) implements OccasionInterface {
            public function __construct(
                private string $startDate,
                private string $endDate,
            ) {}

            public function getStartDate(): string
            {
                return $this->startDate;
            }

            public function getEndDate(): string
            {
                return $this->endDate;
            }

            public function getEndTime(): string
            {
                return '';
            }

            public function isCurrent(): bool
            {
                return true;
            }

            public function getUrl(): string
            {
                return 'https://example.com/event';
            }
        };
    }
}
