<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\Mappers;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapExhibitionEventDatesTest extends TestCase
{
    #[TestDox('Yields no values when schema is not ExhibitionEvent')]
    public function testNonExhibitionEventSchema(): void
    {
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map(Schema::thing()));

        static::assertEmpty($results);
    }

    #[TestDox('Yields no values when ExhibitionEvent has no startDate or endDate')]
    public function testExhibitionEventWithNoDates(): void
    {
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map(Schema::exhibitionEvent()));

        static::assertEmpty($results);
    }

    #[TestDox('Yields startDate for ExhibitionEvent')]
    public function testExhibitionEventWithStartDate(): void
    {
        $schema  = Schema::exhibitionEvent()->startDate('2026-06-04T00:00:00+00:00');
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map($schema));

        static::assertCount(1, $results);
        static::assertSame('startDate', $results[0]->getKey());
        static::assertSame('2026-06-04 00:00:00', $results[0]->getValue());
    }

    #[TestDox('Yields both startDate and endDate for ExhibitionEvent')]
    public function testExhibitionEventWithStartAndEndDate(): void
    {
        $schema  = Schema::exhibitionEvent()
            ->startDate('2026-06-04T00:00:00+00:00')
            ->endDate('2026-08-16T00:00:00+00:00');
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map($schema));

        static::assertCount(2, $results);
        static::assertSame('startDate', $results[0]->getKey());
        static::assertSame('2026-06-04 00:00:00', $results[0]->getValue());
        static::assertSame('endDate', $results[1]->getKey());
        static::assertSame('2026-08-16 00:00:00', $results[1]->getValue());
    }

    #[TestDox('Skips endDate when it is missing')]
    public function testExhibitionEventWithOnlyStartDate(): void
    {
        $schema  = Schema::exhibitionEvent()->startDate('2026-06-04T00:00:00+00:00');
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map($schema));

        static::assertCount(1, $results);
        static::assertSame('startDate', $results[0]->getKey());
    }

    #[TestDox('Converts DateTime objects to MySQL DATETIME format')]
    public function testConvertsDateTimeObjects(): void
    {
        $schema  = Schema::exhibitionEvent()->startDate(new \DateTime('2026-06-04T00:00:00+00:00'));
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map($schema));

        static::assertCount(1, $results);
        static::assertSame('2026-06-04 00:00:00', $results[0]->getValue());
    }

    #[TestDox('Yields no values for Event schema (handled by MapEventStartDates)')]
    public function testEventSchemaIsIgnored(): void
    {
        $schema  = Schema::event()->startDate('2026-06-04T00:00:00+00:00');
        $mapper  = new MapExhibitionEventDates();
        $results = iterator_to_array($mapper->map($schema));

        static::assertEmpty($results);
    }
}
