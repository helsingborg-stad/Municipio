<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\Wpautop;

class MapScheduleDescriptionTest extends TestCase
{
    public function testReturnsEmptyStringWhenNoCurrentlyViewing(): void
    {
        $mapper = new MapScheduleDescription($this->getNullWpService(), null);
        $event  = Schema::event();

        $this->assertEquals('', $mapper->map($event));
    }

    public function testReturnsEmptyStringWhenNoMatchingSchedule(): void
    {
        $currentDate = new DateTime('2024-01-15 10:00:00');
        $mapper      = new MapScheduleDescription($this->getNullWpService(), $currentDate);
        $event       = Schema::event()->eventSchedule([
            Schema::schedule()
                ->startDate(new DateTime('2024-01-16 10:00:00'))
                ->description(['Some description']),
        ]);

        $this->assertEquals('', $mapper->map($event));
    }

    public function testReturnsEmptyStringWhenScheduleHasNoDescription(): void
    {
        $currentDate = new DateTime('2024-01-15 10:00:00');
        $mapper      = new MapScheduleDescription($this->getNullWpService(), $currentDate);
        $event       = Schema::event()->eventSchedule([
            Schema::schedule()->startDate(new DateTime('2024-01-15 10:00:00')),
        ]);

        $this->assertEquals('', $mapper->map($event));
    }

    public function testMapsScheduleDescriptionWithPlainText(): void
    {
        $currentDate = new DateTime('2024-01-15 10:00:00');
        $mapper      = new MapScheduleDescription($this->getNullWpService(), $currentDate);
        $event       = Schema::event()->eventSchedule([
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 10:00:00'))
                ->description(['This is the schedule description.']),
        ]);

        $this->assertEquals('This is the schedule description.', $mapper->map($event));
    }

    public function testMapsScheduleDescriptionWithTextObject(): void
    {
        $currentDate = new DateTime('2024-01-15 10:00:00');
        $mapper      = new MapScheduleDescription($this->getNullWpService(), $currentDate);
        $event       = Schema::event()->eventSchedule([
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 10:00:00'))
                ->description([Schema::textObject()->text('Text object description.')]),
        ]);

        $this->assertEquals('Text object description.', $mapper->map($event));
    }

    public function testFindsCorrectScheduleFromMultiple(): void
    {
        $currentDate = new DateTime('2024-01-15 14:00:00');
        $mapper      = new MapScheduleDescription($this->getNullWpService(), $currentDate);
        $event       = Schema::event()->eventSchedule([
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 10:00:00'))
                ->description(['Morning session']),
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 14:00:00'))
                ->description(['Afternoon session']),
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 18:00:00'))
                ->description(['Evening session']),
        ]);

        $this->assertEquals('Afternoon session', $mapper->map($event));
    }

    public function testAppliesWpautopToDescription(): void
    {
        $currentDate = new DateTime('2024-01-15 10:00:00');
        $wpService   = new class implements Wpautop {
            public function wpautop(string $text, bool $br = true): string
            {
                return '<p>' . $text . '</p>';
            }
        };
        $mapper = new MapScheduleDescription($wpService, $currentDate);
        $event  = Schema::event()->eventSchedule([
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 10:00:00'))
                ->description(['Schedule description']),
        ]);

        $this->assertEquals('<p>Schedule description</p>', $mapper->map($event));
    }

    public function testHandlesStringDescriptionNotInArray(): void
    {
        $currentDate = new DateTime('2024-01-15 10:00:00');
        $mapper      = new MapScheduleDescription($this->getNullWpService(), $currentDate);
        $event       = Schema::event()->eventSchedule([
            Schema::schedule()
                ->startDate(new DateTime('2024-01-15 10:00:00'))
                ->description('Single string description'),
        ]);

        $this->assertEquals('Single string description', $mapper->map($event));
    }

    private function getNullWpService(): Wpautop
    {
        return new class implements Wpautop {
            public function wpautop(string $text, bool $br = true): string
            {
                return $text;
            }
        };
    }
}
