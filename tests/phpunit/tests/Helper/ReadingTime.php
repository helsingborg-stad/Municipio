<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\ReadingTime;
use WP_Mock;

/**
 * Class ListingTest
 * @group wp_mock
 */
class ReadingTimeTest extends TestCase
{
    /**
     * @testdox getReadingTime returns 0 when provided with an empty string.
    */
    public function testGetReadingTimeReturnsZeroWhenProvidedWithEmptyString()
    {
        // When
        $result = ReadingTime::getReadingTime('');

        // Then
        $this->assertEquals(0, $result);
    }

    /**
     * @testdox getReadingTime returns number based on words/number. Always provide a whole number.
     * @dataProvider numberProvider
    */
    public function testGetReadingTimeReturnsNumberBasedOnReadingSpeedAndAmountOfWords($number)
    {
        // When
        $result = ReadingTime::getReadingTime('Etiam Nullam Dolor Malesuada', $number);

        // Then
        $this->assertEquals(2, $result);
    }

    /**
     * @testdox getReadingTime handles dividing by 0.
    */
    public function testGetReadingTimeHandlesDividedByZero()
    {
        // When
        $result = ReadingTime::getReadingTime('Etiam Nullam Dolor Malesuada', 0);

        // Then
        $this->assertIsNumeric($result);
    }

    /**
     * @testdox getReadingTime handles dividing by 0.
    */
    public function testGetReadingTimeReturnsStringWhenWordCountHigherThanReadingSpeed()
    {
        // When
        $result = ReadingTime::getReadingTime('Etiam Nullam Dolor Malesuada', 2, true);

        // Then
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @testdox getReadingTime returns a string when last parameter is set to true and no
    */
    public function testGetReadingTimeReturnsStringWhenWordCountLowerThanReadingSpeed()
    {
        // When
        $result = ReadingTime::getReadingTime('Test', 200, true);

        // Then
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Number provider
    */
    public function numberProvider()
    {
        return [
            [2],
            [3.4],
            [2.2]
        ];
    }
}
