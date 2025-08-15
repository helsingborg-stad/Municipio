<?php

declare(strict_types=1);

namespace Municipio\SchemaData\Utils\OpeningHoursSpecificationToString;

use Municipio\Schema\DayOfWeek;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

/**
 * @covers \Municipio\Schema\Utils\OpeningHoursSpecificationToString\OpeningHoursSpecificationToString
 */
class OpeningHoursSpecificationToStringTest extends TestCase
{
    private function createSpecification(array|string $days, string $opens, string $closes)
    {
        return Schema::openingHoursSpecification()
            ->dayOfWeek($days)
            ->opens($opens)
            ->closes($closes);
    }

    private function convertSpecification($spec): array
    {
        $converter = new OpeningHoursSpecificationToString();
        return $converter->convert($spec);
    }

    public function testConsecutiveDays(): void
    {
        $spec = $this->createSpecification(
            [DayOfWeek::Monday, DayOfWeek::Tuesday, DayOfWeek::Wednesday],
            '09:00:00',
            '18:00:00'
        );

        $expected = ['Monday-Wednesday: 09:00-18:00'];
        $actual   = $this->convertSpecification($spec);

        $this->assertEquals($expected, $actual);
    }

    public function testSingleDay(): void
    {
        $spec = $this->createSpecification(
            DayOfWeek::Monday,
            '09:00:00',
            '18:00:00'
        );

        $expected = ['Monday: 09:00-18:00'];
        $actual   = $this->convertSpecification($spec);

        $this->assertEquals($expected, $actual);
    }

    public function testNonConsecutiveDays(): void
    {
        $spec = $this->createSpecification(
            [DayOfWeek::Monday, DayOfWeek::Wednesday],
            '09:00:00',
            '18:00:00'
        );

        $expected = [
            'Monday: 09:00-18:00',
            'Wednesday: 09:00-18:00'
        ];
        $actual   = $this->convertSpecification($spec);

        $this->assertEquals($expected, $actual);
    }

    public function testCustomDays(): void
    {
        $spec = Schema::openingHoursSpecification()
            ->name('New Years Day')
            ->opens('10:00:00')
            ->closes('17:00:00');

        $expected = ['New Years Day: 10:00-17:00'];
        $actual   = $this->convertSpecification($spec);

        $this->assertEquals($expected, $actual);
    }
}
