<?php

namespace Municipio\Controller;

use Municipio\Helper\WpService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchiveTest extends TestCase
{
    /**
     * @testdox tryFormatDateToUnixTimestamp() returns a unix timestamp when given a valid date string
     * @dataProvider provideValidDateStrings
     */
    public function testTryFormatDateToUnixTimestampReturnsUnixTimestampFromString($dateString)
    {
        WpService::set($this->getWpServiceWithTranslatedDateStrings());
        $archive = $this->getArchiveWithoutConstructor();
        $this->assertIsInt($archive->tryFormatDateToUnixTimestamp($dateString));
    }

    /**
     * @testdox tryFormatDateToUnixTimestamp() returns null on invalid date string
     */
    public function testTryFormatDateToUnixTimestampReturnsNullOnInvalidString()
    {
        $archive = $this->getArchiveWithoutConstructor();
        $this->assertNull($archive->tryFormatDateToUnixTimestamp('invalid date string'));
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

    public function provideValidDateStrings(): array
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

    private function getArchiveWithoutConstructor(): Archive|MockObject
    {
        return $this->getMockBuilder(Archive::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__construct'])
            ->getMock();
    }
}
