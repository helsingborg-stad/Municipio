<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Event;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPlaceNameTest extends TestCase
{
    #[TestDox("returns null if event has no location")]
    public function testGetPlaceNameReturnsNullIfNoLocation()
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                return Schema::event();
            }
        };

        $getPlaceName = new GetPlaceName();
        $callable     = $getPlaceName->getCallable();
        $result       = $callable($post);

        $this->assertNull($result);
    }

    #[TestDox("returns place name if event has location")]
    public function testGetPlaceNameReturnsPlaceNameIfLocationExists()
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                $place = Schema::place()->name('Test Place');
                return Schema::event()->location([$place]);
            }
        };

        $getPlaceName = new GetPlaceName();
        $callable     = $getPlaceName->getCallable();
        $result       = $callable($post);

        $this->assertEquals('Test Place', $result);
    }

    #[TestDox("returns place address if place has no name")]
    public function testGetPlaceNameReturnsPlaceAddressIfNameIsEmpty()
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                $place = Schema::place()->address('Test Address');
                return Schema::event()->location([$place]);
            }
        };

        $getPlaceName = new GetPlaceName();
        $callable     = $getPlaceName->getCallable();
        $result       = $callable($post);

        $this->assertEquals('Test Address', $result);
    }

    #[TestDox("returns null if event location name and address is empty")]
    public function testGetPlaceNameReturnsNullIfLocationNameAndAddressIsEmpty()
    {
        $post = new class extends \Municipio\PostObject\NullPostObject {
            public function getSchema(): \Municipio\Schema\BaseType
            {
                $place = Schema::place();
                return Schema::event()->location([$place]);
            }
        };

        $getPlaceName = new GetPlaceName();
        $callable     = $getPlaceName->getCallable();
        $result       = $callable($post);

        $this->assertNull($result);
    }
}
