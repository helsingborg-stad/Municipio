<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Controller\SingularEvent;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapOccasionsTest extends TestCase
{
    /**
     * @testdox returns an array of Occasion objects
     */
    public function testReturnsArrayOfOccasionObjects()
    {
        $event = Schema::event()->eventSchedule([
        Schema::schedule()->startDate(new \DateTime('2025-12-24 18:00')),
        ]);

        $mapper    = new MapOccasions('', null);
        $occasions = $mapper->map($event);

        $this->assertIsArray($occasions);
        $this->assertCount(1, $occasions);
        $this->assertInstanceOf(OccasionInterface::class, $occasions[0]);
    }

    /**
     * @testdox appends indicator to occasion url
     */
    public function testAppendsIndicatorToOccasionUrl()
    {
        $event = Schema::event()->eventSchedule([
        Schema::schedule()->startDate(new \DateTime('2025-12-24 18:00')),
        ]);

        $mapper    = new MapOccasions('https://example.com/event', null);
        $occasions = $mapper->map($event);
        $url       = $occasions[0]->getUrl();

        $this->assertStringStartsWith('https://example.com/event', $url);
        // Ensure the URL contains the GET param for current occasion
        $this->assertStringContainsString('?' . SingularEvent::CURRENT_OCCASSION_GET_PARAM, $url);
        // Ensure the date value is present
        $this->assertNotEmpty(explode('=', $url)[1]);
    }

    /**
     * @testdox marks the correct occasion as current based on provided DateTime
     */
    public function testMarksCorrectOccasionAsCurrent()
    {
        $event = Schema::event()->eventSchedule([
            Schema::schedule()->startDate(new \DateTime('2025-12-24 18:00')),
            Schema::schedule()->startDate(new \DateTime('2025-12-25 18:00')),
        ]);

        $currentlyViewing = new \DateTime('2025-12-25 18:00');
        $mapper           = new MapOccasions('https://example.com/event', $currentlyViewing);
        $occasions        = $mapper->map($event);

        $this->assertFalse($occasions[0]->isCurrent());
        $this->assertTrue($occasions[1]->isCurrent());
    }
}
