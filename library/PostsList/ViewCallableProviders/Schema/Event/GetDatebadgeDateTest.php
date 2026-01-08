<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTime;
use Municipio\Helper\DateFormat;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetDatebadgeDateTest extends TestCase
{
    #[TestDox('Gets a date from the first upcoming event')]
    public function testGetDatebadgeDate(): void
    {
        $firstAvailableDate = new DateTime('+1 day');
        $post = new class($firstAvailableDate) extends \Municipio\PostObject\NullPostObject {
            public function __construct(
                private DateTime $firstAvailableDate,
            ) {}

            public function getSchema(): \Municipio\Schema\BaseType
            {
                $schedule1 = Schema::schedule()->startDate($this->firstAvailableDate);
                $schedule2 = Schema::schedule()->startDate(new DateTime('+2 days'));
                return Schema::event()->eventSchedule([$schedule1, $schedule2]);
            }
        };

        $getDatebadgeDate = new GetDatebadgeDate();
        $callable = $getDatebadgeDate->getCallable();
        $result = $callable($post);

        $this->assertEquals($firstAvailableDate->format(DateFormat::getDateFormat('date')), $result);
    }

    #[TestDox('returns closest passed schedule if no upcoming events are found')]
    public function testGetDatebadgeDateNoUpcomingEvents(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                $schedule1 = Schema::schedule()->startDate(new DateTime('-1 day'));
                $schedule2 = Schema::schedule()->startDate(new DateTime('-2 days'));
                return Schema::event()->eventSchedule([$schedule1, $schedule2]);
            }
        };

        $getDatebadgeDate = new GetDatebadgeDate();
        $callable = $getDatebadgeDate->getCallable();
        $result = $callable($post);

        $this->assertEquals((new DateTime('-1 day'))->format(DateFormat::getDateFormat('date')), $result);
    }

    #[TestDox('returns null if no schedules are provided')]
    public function testReturnsNullIfNoSchedulesProvided(): void
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                return Schema::event()->eventSchedule([]);
            }
        };

        $getDatebadgeDate = new GetDatebadgeDate();
        $callable = $getDatebadgeDate->getCallable();
        $result = $callable($post);

        $this->assertNull($result);
    }

    #[TestDox('returns next upcoming date relative to supplied date')]
    public function testReturnsNextUpcomingDateRelativeToSuppliedDate(): void
    {
        $referenceDate = new DateTime('+3 days');
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                $schedule1 = Schema::schedule()->startDate(new DateTime('+1 day'));
                $schedule2 = Schema::schedule()->startDate(new DateTime('+4 days'));
                return Schema::event()->eventSchedule([$schedule1, $schedule2]);
            }
        };

        $getDatebadgeDate = new GetDatebadgeDate($referenceDate->format('Y-m-d H:i:s'));
        $callable = $getDatebadgeDate->getCallable();
        $result = $callable($post);

        $this->assertEquals((new DateTime('+4 days'))->format(DateFormat::getDateFormat('date')), $result);
    }
}
