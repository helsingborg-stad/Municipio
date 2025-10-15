<?php

namespace Municipio\Controller\ArchiveEvent;

use DateTime;
use Municipio\Helper\DateFormat;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetDatebadgeDateTest extends TestCase
{
    #[TestDox("Gets a date from the first upcoming event")]
    public function testGetDatebadgeDate(): void
    {
        $firstAvailableDate = new DateTime('+1 day');
        $event              = Schema::event()->eventSchedule([
            Schema::schedule()->startDate($firstAvailableDate),
            Schema::schedule()->startDate(new DateTime('+2 days')),
        ]);

        $result = GetDatebadgeDate::getDatebadgeDate($event);

        $this->assertEquals($firstAvailableDate->format(DateFormat::getDateFormat('Y-m-d')), $result);
    }

    #[TestDox("returns null if no upcoming events are found")]
    public function testGetDatebadgeDateNoUpcomingEvents(): void
    {
        $event = Schema::event()->eventSchedule([
            Schema::schedule()->startDate(new DateTime('-1 day')),
            Schema::schedule()->startDate(new DateTime('-2 days')),
        ]);

        $this->assertNull(GetDatebadgeDate::getDatebadgeDate($event));
    }

    #[TestDox("returns null if no schedules are provided")]
    public function testReturnsNullIfNoSchedulesProvided(): void
    {
        $event = Schema::event()->eventSchedule([]);

        $this->assertNull(GetDatebadgeDate::getDatebadgeDate($event));
    }
}
