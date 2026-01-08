<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use DateTimeImmutable;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\DateI18n;

class GetDateTest extends TestCase
{
    #[TestDox('returns null when no schedules')]
    public function testReturnsNullWhenNoSchedules()
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): BaseType
            {
                return Schema::event()->eventSchedule([]);
            }
        };

        $getDate = new GetDate(self::createWpService());
        $result = $getDate->getCallable()($post);

        $this->assertNull($result);
    }

    #[TestDox('returns single date when one passed schedule')]
    public function testReturnsSingleDateWhenOneUpcomingSchedule()
    {
        $date = new DateTimeImmutable('+1 day');
        $post = new class($date) extends \Municipio\PostObject\NullPostObject {
            public function __construct(
                private DateTimeImmutable $date,
            ) {}

            public function getSchema(): BaseType
            {
                $schedule = Schema::schedule()->startDate($this->date)->endDate($this->date);
                return Schema::event()->eventSchedule([$schedule]);
            }
        };
        $getDate = new GetDate(self::createWpService());
        $result = $getDate->getCallable()($post);

        $expected = $date->format('Y-m-d H:i');

        $this->assertEquals($expected, $result);
    }

    #[TestDox('returns closest passed date when no upcoming schedules')]
    public function testReturnsClosestPassedDateWhenNoUpcomingSchedules()
    {
        $passedDate = new DateTimeImmutable('-1 day');
        $post = new class($passedDate) extends \Municipio\PostObject\NullPostObject {
            public function __construct(
                private DateTimeImmutable $passedDate,
            ) {}

            public function getSchema(): BaseType
            {
                $schedule1 = Schema::schedule()->startDate(new DateTimeImmutable('-2 days'))->endDate(new DateTimeImmutable('-2 days'));
                $schedule2 = Schema::schedule()->startDate($this->passedDate)->endDate($this->passedDate);
                return Schema::event()->eventSchedule([$schedule1, $schedule2]);
            }
        };
        $getDate = new GetDate(self::createWpService());
        $result = $getDate->getCallable()($post);

        $expected = $passedDate->format('Y-m-d H:i');

        $this->assertEquals($expected, $result);
    }

    #[TestDox('returns next upcoming date relative to supplied date')]
    public function testReturnsNextUpcomingDateRelativeToSuppliedDate()
    {
        $upcomingDate = new DateTimeImmutable('+10 days');
        $post = new class($upcomingDate) extends \Municipio\PostObject\NullPostObject {
            public function __construct(
                private DateTimeImmutable $upcomingDate,
            ) {}

            public function getSchema(): BaseType
            {
                $schedule1 = Schema::schedule()->startDate(new DateTimeImmutable('+5 days'))->endDate(new DateTimeImmutable('+5 days'));
                $schedule2 = Schema::schedule()->startDate($this->upcomingDate)->endDate($this->upcomingDate);
                return Schema::event()->eventSchedule([$schedule1, $schedule2]);
            }
        };

        $getDate = new GetDate(self::createWpService(), $upcomingDate->format('Y-m-d H:i'));
        $result = $getDate->getCallable()($post);
        $expected = $upcomingDate->format('Y-m-d H:i');

        static::assertSame($expected, $result);
    }

    private static function createWpService(): DateI18n
    {
        return new class implements DateI18n {
            public function dateI18n(string $format, int|bool $timestampWithOffset = false, bool $gmt = false): string
            {
                return date($format, $timestampWithOffset);
            }
        };
    }
}
