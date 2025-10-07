<?php

namespace Municipio\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class StringToTimeTest extends TestCase
{
    #[TestDox("tryFormatDateToUnixTimestamp() returns a unix timestamp when given a valid date string")]
    #[DataProvider("provideValidDateStrings")]
    public function testTryFormatDateToUnixTimestampReturnsUnixTimestampFromString($dateString)
    {
        $this->assertIsInt((new StringToTime($this->getWpServiceWithTranslatedDateStrings()))->convert($dateString));
    }

    #[TestDox('tryFormatDateToUnixTimestamp() returns the same int as provided')]
    public function testTryFormatDateToUnixTimestampReturnsSameIntAsProvided()
    {
        $this->assertEquals(1234567890, (new StringToTime(new FakeWpService(['__' => 'string'])))->convert(1234567890));
    }

    #[TestDox('tryFormatDateToUnixTimestamp() returns null on invalid date string')]
    public function testTryFormatDateToUnixTimestampReturnsNullOnInvalidString()
    {
        $this->assertNull((new StringToTime(new FakeWpService(['__' => 'not a valid string'])))->convert('invalid date string'));
    }

    private function getWpServiceWithTranslatedDateStrings()
    {
        return new FakeWpService(['__' => fn($string) =>
            match ($string) {
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Mars',
                'April' => 'April',
                'May' => 'Maj',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Augusti',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'December',
                'Jan' => 'Jan',
                'Feb' => 'Feb',
                'Mar' => 'Mar',
                'Apr' => 'Apr',
                'May' => 'Maj',
                'Jun' => 'Jun',
                'Jul' => 'Jul',
                'Aug' => 'Aug',
                'Sep' => 'Sep',
                'Oct' => 'Okt',
                'Nov' => 'Nov',
                'Dec' => 'Dec',
                'Monday' => 'Måndag',
                'Tuesday' => 'Tisdag',
                'Wednesday' => 'Onsdag',
                'Thursday' => 'Torsdag',
                'Friday' => 'Fredag',
                'Saturday' => 'Lördag',
                'Sunday' => 'Söndag',
                'Mon' => 'Mån',
                'Tue' => 'Tis',
                'Wed' => 'Ons',
                'Thu' => 'Tor',
                'Fri' => 'Fre',
                'Sat' => 'Lör',
                'Sun' => 'Sön',
            }
        ]);
    }

    public static function provideValidDateStrings(): array
    {
        return [
            '2020-01-01'              => ['2020-01-01'],
            '2020-01-01 00:00:00'     => ['2020-01-01 00:00:00'],
            '2020-01-01 00:00:00.000' => ['2020-01-01 00:00:00.000'],
            '2020-01-01T00:00:00'     => ['2020-01-01T00:00:00'],
            '2020-01-01T00:00:00.000' => ['2020-01-01T00:00:00.000'],
            'January 1, 2020'         => ['January 1, 2020'],
            'Januari 1, 2020'         => ['Januari 1, 2020'],
            'Okt 1, 2020'             => ['Okt 1, 2020'],
            'Monday, January 1, 2020' => ['Monday, January 1, 2020'],
            'Tisdag, Januari 2, 2020' => ['Tisdag, Januari 2, 2020'],
            'Mon, January 1, 2020'    => ['Mon, January 1, 2020'],
            'Mån, Januari 27, 2025'   => ['Mån, Januari 27, 2025'],
            'mån, januari 27, 2025'   => ['mån, januari 27, 2025'],
        ];
    }
}
