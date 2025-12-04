<?php

namespace Municipio\Controller\SingularEvent;

use PHPUnit\Framework\TestCase;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schedule;
use DateTimeImmutable;
use Municipio\Schema\Schema;

class GetEventDateTest extends TestCase
{
    public function testReturnsNullWhenNoSchedules()
    {
        $event = Schema::event()->eventSchedule([]);

        $this->assertNull(GetEventDate::getEventDate($event));
    }

    public function testReturnsSingleDateWhenOneUpcomingSchedule()
    {
        $date     = new DateTimeImmutable('+1 day');
        $schedule = Schema::schedule()->startDate($date)->endDate($date);
        $event    = Schema::event()->eventSchedule([$schedule]);

        $expected = $date->format('Y-m-d');
        $this->assertEquals($expected, GetEventDate::getEventDate($event));
    }

    public function testReturnsDateRangeWhenMultipleUpcomingSchedules()
    {
        $startDate = new DateTimeImmutable('+1 day');
        $endDate   = new DateTimeImmutable('+2 day');

        $schedule1 = Schema::schedule()->startDate($startDate)->endDate($endDate);
        $schedule2 = Schema::schedule()->startDate($startDate)->endDate($endDate);
        $event     = Schema::event()->eventSchedule([$schedule1, $schedule2]);

        $expected = $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
        $this->assertEquals($expected, GetEventDate::getEventDate($event));
    }
}
