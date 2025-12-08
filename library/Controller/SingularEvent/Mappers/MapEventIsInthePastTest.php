<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class MapEventIsInthePastTest extends TestCase
{
    #[TestDox('returns true if event startDate is in the past')]
    public function testReturnsTrueIfEventIsInThePast()
    {
        $mapper = new MapEventIsInthePast((new DateTime())->modify('-1 day'));
        $this->assertTrue($mapper->map(Schema::event()));
    }

    #[TestDox('returns false if event startDate is in the future')]
    public function testReturnsFalseIfEventIsInTheFuture()
    {
        $mapper = new MapEventIsInthePast((new DateTime())->modify('+1 day'));
        $this->assertFalse($mapper->map(Schema::event()));
    }

    #[TestDox('returns false if event startDate is not a DateTime object')]
    public function testReturnsFalseIfEventStartDateIsNotDateTime()
    {
        $mapper = new MapEventIsInthePast();
        $this->assertFalse($mapper->map(Schema::event()));
    }
}
