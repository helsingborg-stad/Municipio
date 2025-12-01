<?php

namespace Municipio\Controller\SingularEvent;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetEventPlaceNameTest extends TestCase
{
    #[TestDox("returns null if event has no location")]
    public function testGetEventPlaceNameReturnsNullIfNoLocation()
    {
        $this->assertNull(GetEventPlaceName::getEventPlaceName(Schema::event()));
    }

    #[TestDox("returns place name if event has location")]
    public function testGetEventPlaceNameReturnsPlaceNameIfLocationExists()
    {
        $event = Schema::event()->location([
            Schema::place()->name('Test Place')
        ]);

        $this->assertEquals('Test Place', GetEventPlaceName::getEventPlaceName($event));
    }

    #[TestDox("returns place address if place has no name")]
    public function testGetEventPlaceNameReturnsPlaceAddressIfNameIsEmpty()
    {
        $event = Schema::event()->location([
            Schema::place()->address('Test Address')
        ]);

        $this->assertEquals('Test Address', GetEventPlaceName::getEventPlaceName($event));
    }

    #[TestDox("returns null if event location name and address is empty")]
    public function testGetEventPlaceNameReturnsNullIfLocationNameAndAddressIsEmpty()
    {
        $event = Schema::event()->location([Schema::place()]);

        $this->assertNull(GetEventPlaceName::getEventPlaceName($event));
    }
}
