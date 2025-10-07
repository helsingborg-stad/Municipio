<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Controller\SingularEvent\Mappers\Occasion\Occasion;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapCurrentOccasionTest extends TestCase
{
    /**
     * @testdox returns null when no occasions are provided
     */
    public function testReturnsNullWhenNoOccasionsProvided()
    {
        $mapper = new MapCurrentOccasion();
        $result = $mapper->map(Schema::event()->eventSchedule([]));

        $this->assertNull($result);
    }

    /**
     * @testdox returns the correct current occasion based on provided DateTime
     */
    public function testReturnsCorrectCurrentOccasion()
    {
        $occasion1 = new Occasion('', false, '');
        $occasion2 = new Occasion('', true, '');
        $occasion3 = new Occasion('', false, '');

        $mapper = new MapCurrentOccasion($occasion1, $occasion2, $occasion3);
        $result = $mapper->map(Schema::event());

        $this->assertSame($occasion2, $result);
    }

    /**
     * @testdox returns null when no occasion is marked as current
     */
    public function testReturnsNullWhenNoCurrentOccasion()
    {
        $occasion1 = new Occasion('', false, '');

        $mapper = new MapCurrentOccasion($occasion1);
        $result = $mapper->map(Schema::event());

        $this->assertNull($result);
    }
}
