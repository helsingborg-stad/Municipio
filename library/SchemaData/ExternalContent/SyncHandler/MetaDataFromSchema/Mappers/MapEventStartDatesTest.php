<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\Mappers;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapEventStartDatesTest extends TestCase
{
    #[TestDox('Yields no values when schema type is not Event')]
    public function testNonEventSchema(): void
    {
        $nonEventSchema = Schema::thing();
        $mapper = new MapEventStartDates();
        $results = iterator_to_array($mapper->map($nonEventSchema));

        static::assertEmpty($results);
    }

    #[TestDox('Yields startDate values for each schedule in Event schema')]
    public function testEventSchemaWithSchedules(): void
    {
        $eventSchema = Schema::event()->eventSchedule([
            Schema::schedule()->startDate('2024-01-01T10:00:00Z'),
            Schema::schedule()->startDate('2024-02-01T10:00:00Z'),
        ]);

        $mapper = new MapEventStartDates();
        $results = iterator_to_array($mapper->map($eventSchema));

        static::assertCount(2, $results);
        static::assertSame('2024-01-01 10:00:00', $results[0]->getValue());
        static::assertSame('2024-02-01 10:00:00', $results[1]->getValue());
    }

    #[TestDox('Yields items with startDate as key')]
    public function testMetaDataItemKeys(): void
    {
        $eventSchema = Schema::event()->eventSchedule([
            Schema::schedule()->startDate('2024-01-01T10:00:00Z'),
        ]);

        $mapper = new MapEventStartDates();
        $results = iterator_to_array($mapper->map($eventSchema));

        static::assertCount(1, $results);
        static::assertSame('startDate', $results[0]->getKey());
    }

    #[TestDox('Skips schedules without startDate property')]
    public function testEventSchemaWithSomeSchedulesMissingStartDate(): void
    {
        $eventSchema = Schema::event()->eventSchedule([
            Schema::schedule()->startDate('2024-01-01T10:00:00Z'),
            Schema::schedule(), // No startDate
            Schema::schedule()->startDate('2024-02-01T10:00:00Z'),
        ]);

        $mapper = new MapEventStartDates();
        $results = iterator_to_array($mapper->map($eventSchema));

        static::assertCount(2, $results);
        static::assertSame('2024-01-01 10:00:00', $results[0]->getValue());
        static::assertSame('2024-02-01 10:00:00', $results[1]->getValue());
    }

    #[TestDox('Converts DateTime objects to ISO 8601 strings')]
    public function testEventSchemaWithDateTimeObjects(): void
    {
        $eventSchema = Schema::event()->eventSchedule([
            Schema::schedule()->startDate(new \DateTime('2024-01-01T10:00:00Z')),
        ]);

        $mapper = new MapEventStartDates();
        $results = iterator_to_array($mapper->map($eventSchema));

        static::assertCount(1, $results);
        static::assertSame('2024-01-01 10:00:00', $results[0]->getValue());
    }

    #[TestDox('Converts non ISO 8601 date strings to ISO 8601 format')]
    public function testEventSchemaWithNonIsoDateStrings(): void
    {
        $eventSchema = Schema::event()->eventSchedule([
            Schema::schedule()->startDate('2024-01-01 10:00:00'),
        ]);

        $mapper = new MapEventStartDates();
        $results = iterator_to_array($mapper->map($eventSchema));

        static::assertCount(1, $results);
        static::assertSame('2024-01-01 10:00:00', $results[0]->getValue());
    }
}
