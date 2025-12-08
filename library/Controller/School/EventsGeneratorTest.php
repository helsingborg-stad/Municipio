<?php

namespace Municipio\Controller\School;

use DateTime;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class EventsGeneratorTest extends TestCase {

    #[TestDox('It generates occasions only for schedules with start dates today or later')]
    public function testGenerateOccasions(): void 
    {
        $preschool = Schema::preschool()->event(Schema::event()->name('Event')->eventSchedule([
            Schema::schedule()->startDate(new DateTime('-1 days'))->endDate(new DateTime('-1 days')),
            Schema::schedule()->startDate(new DateTime('today'))->endDate(new DateTime('today')),
            Schema::schedule()->startDate(new DateTime('+1 days'))->endDate(new DateTime('+1 days')),
        ]));

        $eventsGenerator = new EventsGenerator($preschool);
        $occasions = $eventsGenerator->generate();

        $this->assertCount(2, $occasions);
        $this->assertEquals( (new DateTime('today'))->getTimestamp() ,$occasions[0]['timestamp']);
        $this->assertEquals( (new DateTime('+1 days'))->getTimestamp() ,$occasions[1]['timestamp']);
    }
}