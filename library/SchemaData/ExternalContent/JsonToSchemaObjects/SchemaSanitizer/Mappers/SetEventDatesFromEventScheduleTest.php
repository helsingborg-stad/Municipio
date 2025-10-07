<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects\SchemaSanitizer\Mappers;

use DateTime;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class SetEventDatesFromEventScheduleTest extends TestCase {
    #[TestDox('sets Event startDate and endDate from eventSchedules property by using the first future startDate found')]
    public function testSetsEventStartDateFromEventSchedules(): void {
        $event = Schema::event()->eventSchedule([
            Schema::schedule()->startDate(new DateTime('-2 days')),
            Schema::schedule()->startDate($firstFutureStart = new DateTime('+1 day'))->endDate($firstFutureEnd = new DateTime('+1 day +2 hours')),
            Schema::schedule()->startDate(new DateTime('+2 days')),
        ]);

        $mapper = new SetEventDatesFromEventSchedule();
        $event = $mapper->map($event);

        $this->assertSame( $firstFutureStart, $event->getProperty('startDate') );
        $this->assertSame( $firstFutureEnd, $event->getProperty('endDate') );
    }

    #[TestDox('sets Event startDate and endDate from eventSchedules property by using the closest past startDate if no future startDate exists')]
    public function testSetsEventStartDateFromEventSchedulesWithNoFutureDates(): void {
        $event = Schema::event()->eventSchedule([
            Schema::schedule()->startDate(new DateTime('-2 days')),
            Schema::schedule()->startDate($closestPastStart = new DateTime('-1 day'))->endDate($closestPastEnd = new DateTime('-1 day +2 hours')),
        ]);

        $mapper = new SetEventDatesFromEventSchedule();
        $event = $mapper->map($event);

        $this->assertSame( $closestPastStart, $event->getProperty('startDate') );
        $this->assertSame( $closestPastEnd, $event->getProperty('endDate') );
    }
}