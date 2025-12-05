<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class GetDateTest extends TestCase
{
    public function testReturnsNullWhenNoSchedules()
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): BaseType
            {
                return Schema::event()->eventSchedule([]);
            }
        };

        $getDate = new GetDate();
        $result  = $getDate->getCallable()($post);

        $this->assertNull($result);
    }

    public function testReturnsSingleDateWhenOneUpcomingSchedule()
    {
        $date    = new DateTimeImmutable('+1 day');
        $post    = new class ($date) extends \Municipio\PostObject\NullPostObject {
            public function __construct(private DateTimeImmutable $date)
            {
            }
            public function getSchema(): BaseType
            {
                $schedule = Schema::schedule()->startDate($this->date)->endDate($this->date);
                return Schema::event()->eventSchedule([$schedule]);
            }
        };
        $getDate = new GetDate();
        $result  = $getDate->getCallable()($post);

        $expected = $date->format('Y-m-d');

        $this->assertEquals($expected, $result);
    }

    public function testReturnsDateRangeWhenMultipleUpcomingSchedules()
    {
        $startDate = new DateTimeImmutable('+1 day');
        $endDate   = new DateTimeImmutable('+2 day');

        $post    = new class ($startDate, $endDate) extends \Municipio\PostObject\NullPostObject {
            public function __construct(private DateTimeImmutable $startDate, private DateTimeImmutable $endDate)
            {
            }
            public function getSchema(): BaseType
            {
                $schedule1 = Schema::schedule()->startDate($this->startDate)->endDate($this->startDate);
                $schedule2 = Schema::schedule()->startDate($this->endDate)->endDate($this->endDate);
                return Schema::event()->eventSchedule([$schedule1, $schedule2]);
            }
        };
        $getDate = new GetDate();
        $result  = $getDate->getCallable()($post);

        $expected = $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
        $this->assertEquals($expected, $result);
    }
}
