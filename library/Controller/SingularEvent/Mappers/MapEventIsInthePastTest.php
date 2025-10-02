<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapEventIsInthePastTest extends TestCase
{
    /**
     * @testdox returns true if event startDate is in the past
     */
    public function testReturnsTrueIfEventIsInThePast()
    {
        $event  = Schema::event()->startDate((new DateTime())->modify('-1 day'));
        $mapper = new MapEventIsInthePast();
        $this->assertTrue($mapper->map($event));
    }

    /**
     * @testdox returns false if event startDate is in the future
     */
    public function testReturnsFalseIfEventIsInTheFuture()
    {
        $event  = Schema::event()->startDate((new DateTime())->modify('+1 day'));
        $mapper = new MapEventIsInthePast();
        $this->assertFalse($mapper->map($event));
    }

    /**
     * @testdox returns false if event startDate is not a DateTime object
     */
    public function testReturnsFalseIfEventStartDateIsNotDateTime()
    {
        $event  = Schema::event()->startDate('not-a-date-time');
        $mapper = new MapEventIsInthePast();
        $this->assertFalse($mapper->map($event));
    }
}
