<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapDateFormatTest extends TestCase
{
    #[TestDox('returns DATE when format is "date"')]
    public function testMapWithDateFormatDate()
    {
        $mapper = new MapDateFormat();

        $result = $mapper->map(['archiveProps' => (object) ['dateFormat' => 'date']]);
        static::assertSame(DateFormat::DATE, $result);
    }

    #[TestDox('returns DATE_TIME format when format is "date-time"')]
    public function testMapWithDateFormatTime()
    {
        $mapper = new MapDateFormat();

        $result = $mapper->map(['archiveProps' => (object) ['dateFormat' => 'date-time']]);
        static::assertSame(DateFormat::DATE_TIME, $result);
    }

    #[TestDox('returns TIME format when format is "time"')]
    public function testMapWithDateFormatDateTime()
    {
        $mapper = new MapDateFormat();

        $result = $mapper->map(['archiveProps' => (object) ['dateFormat' => 'time']]);
        static::assertSame(DateFormat::TIME, $result);
    }

    #[TestDox('returns default DATE_TIME format when no valid format is provided')]
    public function testMapWithNoDateFormat()
    {
        $mapper = new MapDateFormat();

        $result = $mapper->map([]);
        static::assertSame(DateFormat::DATE_TIME, $result);
    }

    #[TestDox('returns default NONE format when dateField is "none"')]
    public function testMapWithDateFieldNone()
    {
        $mapper = new MapDateFormat();

        $result = $mapper->map(['archiveProps' => (object) ['dateField' => 'none']]);
        static::assertSame(DateFormat::NONE, $result);
    }
}
