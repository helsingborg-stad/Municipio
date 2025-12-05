<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetDateFormatTest extends TestCase
{
    #[TestDox('returns correct date format from config')]
    #[DataProvider('dateFormatProvider')]
    public function testReturnsCorrectDateFormatFromConfig(DateFormat $format, string $expected)
    {
        $provider = new GetDateFormat($format);
        static::assertSame($expected, $provider->getCallable()());
    }

    public static function dateFormatProvider(): array
    {
        return [
            DateFormat::DATE_TIME->value => [DateFormat::DATE_TIME, 'Y-m-d H:i'],
            DateFormat::DATE->value => [DateFormat::DATE, 'Y-m-d'],
            DateFormat::TIME->value => [DateFormat::TIME, 'H:i'],
            DateFormat::DATE_BADGE->value => [DateFormat::DATE_BADGE, 'date-badge'],
        ];
    }
}
