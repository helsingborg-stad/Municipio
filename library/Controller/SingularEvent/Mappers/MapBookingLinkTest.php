<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapBookingLinkTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated()
    {
        $mapper = new MapBookingLink();
        $this->assertInstanceOf(MapBookingLink::class, $mapper);
    }

    #[TestDox('returns booking link from schedule when available')]
    public function testReturnsBookingLinkFromScheduleWhenAvailable()
    {
        $currentScheduleDateTime = new \DateTime('now');
        $event = Schema::event()->eventSchedule([
            Schema::schedule()->url('https://example.com/schedule-link')->startDate($currentScheduleDateTime),
        ]);

        $mapper = new MapBookingLink($currentScheduleDateTime);
        $bookingLink = $mapper->map($event);

        static::assertEquals('https://example.com/schedule-link', $bookingLink);
    }

    #[TestDox('returns booking link from offers when no schedule link is available')]
    public function testReturnsBookingLinkFromOffersWhenNoScheduleLinkIsAvailable()
    {
        $event = Schema::event()->offers([
            Schema::offer()->url('https://example.com/offer-link'),
        ]);

        $mapper = new MapBookingLink();
        $bookingLink = $mapper->map($event);

        static::assertEquals('https://example.com/offer-link', $bookingLink);
    }

    #[TestDox('returns null when no booking link is available')]
    public function testReturnsNullWhenNoBookingLinkIsAvailable()
    {
        $event = Schema::event();

        $mapper = new MapBookingLink();
        $bookingLink = $mapper->map($event);

        static::assertNull($bookingLink);
    }
}
