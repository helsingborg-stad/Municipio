<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use DateTimeImmutable;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapEventIsInthePastTest extends TestCase
{
    #[TestDox('returns true if event endDate is in the past when viewing specific occasion')]
    public function testReturnsTrueIfEventIsInThePastWhenViewingSpecificOccasion()
    {
        $startDate = (new DateTimeImmutable())->modify('-2 hours');
        $endDate = (new DateTimeImmutable())->modify('-1 hour');
        $schedule = Schema::schedule()->startDate($startDate)->endDate($endDate);
        $event = Schema::event()->eventSchedule([$schedule]);

        $mapper = new MapEventIsInthePast(DateTime::createFromInterface($startDate));
        $this->assertTrue($mapper->map($event));
    }

    #[TestDox('returns false if event endDate is in the future when viewing specific occasion')]
    public function testReturnsFalseIfEventIsInTheFutureWhenViewingSpecificOccasion()
    {
        $startDate = (new DateTimeImmutable())->modify('-1 hour');
        $endDate = (new DateTimeImmutable())->modify('+1 hour');
        $schedule = Schema::schedule()->startDate($startDate)->endDate($endDate);
        $event = Schema::event()->eventSchedule([$schedule]);

        $mapper = new MapEventIsInthePast(DateTime::createFromInterface($startDate));
        $this->assertFalse($mapper->map($event));
    }

    #[TestDox('returns true if latest endDate is in the past when not viewing specific occasion')]
    public function testReturnsTrueIfLatestEndDateIsInThePast()
    {
        $schedule1 = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('-2 days'))
            ->endDate((new DateTimeImmutable())->modify('-1 day'));
        $schedule2 = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('-4 days'))
            ->endDate((new DateTimeImmutable())->modify('-3 days'));
        $event = Schema::event()->eventSchedule([$schedule1, $schedule2]);

        $mapper = new MapEventIsInthePast();
        $this->assertTrue($mapper->map($event));
    }

    #[TestDox('returns false if latest endDate is in the future when not viewing specific occasion')]
    public function testReturnsFalseIfLatestEndDateIsInTheFuture()
    {
        $schedule1 = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('+1 day'))
            ->endDate((new DateTimeImmutable())->modify('+2 days'));
        $schedule2 = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('-2 days'))
            ->endDate((new DateTimeImmutable())->modify('-1 day'));
        $event = Schema::event()->eventSchedule([$schedule1, $schedule2]);

        $mapper = new MapEventIsInthePast();
        $this->assertFalse($mapper->map($event));
    }

    #[TestDox('returns false if event has no schedules')]
    public function testReturnsFalseIfEventHasNoSchedules()
    {
        $event = Schema::event()->eventSchedule([]);

        $mapper = new MapEventIsInthePast();
        $this->assertFalse($mapper->map($event));
    }

    #[TestDox('returns false if specific occasion is not found')]
    public function testReturnsFalseIfSpecificOccasionNotFound()
    {
        $schedule = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('-1 day'))
            ->endDate((new DateTimeImmutable())->modify('+1 day'));
        $event = Schema::event()->eventSchedule([$schedule]);

        // Try to view a different occasion that doesn't exist
        $mapper = new MapEventIsInthePast((new DateTime())->modify('+5 days'));
        $this->assertFalse($mapper->map($event));
    }

    #[TestDox('returns true when viewing specific occasion with null endDate but latest endDate is in past')]
    public function testReturnsTrueWhenSpecificOccasionHasNullEndDateButLatestIsInPast()
    {
        $schedule1 = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('-2 days'))
            ->endDate(null);  // No endDate for this schedule
        $schedule2 = Schema::schedule()
            ->startDate((new DateTimeImmutable())->modify('-4 days'))
            ->endDate((new DateTimeImmutable())->modify('-3 days'));
        $event = Schema::event()->eventSchedule([$schedule1, $schedule2]);

        // View the specific occasion that has no endDate
        $mapper = new MapEventIsInthePast(DateTime::createFromInterface((new DateTimeImmutable())->modify('-2 days')));
        $this->assertTrue($mapper->map($event));
    }
}
