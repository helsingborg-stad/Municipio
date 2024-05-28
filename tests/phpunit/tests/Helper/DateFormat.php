<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\DateFormat;
use WP_Mock;

/**
 * Class ListingTest
 * @group wp_mock
 */
class DateFormatTest extends TestCase
{
    /**
     * @testdox getDateFormat returns default value of date + time format.
    */
    public function testGetDateFormatReturnsDefaultValue()
    {
        // Given
        $this->getDefaultMockedData();

        // When
        $result = DateFormat::getDateFormat('');

        // Then
        $this->assertEquals('Y-m-d H:i', $result);
    }

    /**
     * @testdox getDateFormat returns default value of date when date provided as parameter
    */
    public function testGetDateFormatReturnsDefaultDateValue()
    {
        // Given
        $this->getDefaultMockedData();

        // When
        $result = DateFormat::getDateFormat('date');

        // Then
        $this->assertEquals('Y-m-d', $result);
    }

    /**
     * @testdox getDateFormat returns default value of time when time provided as parameter
    */
    public function testGetDateFormatReturnsDefaultTimeValue()
    {
        // Given
        $this->getDefaultMockedData();

        // When
        $result = DateFormat::getDateFormat('time');

        // Then
        $this->assertEquals('H:i', $result);
    }

    /**
     * @testdox getDateFormat returns default value of date + time when date-time provided as parameter
    */
    public function testGetDateFormatReturnsDefaultDateTimeValue()
    {
        // Given
        $this->getDefaultMockedData();

        // When
        $result = DateFormat::getDateFormat('date-time');

        // Then
        $this->assertEquals('Y-m-d H:i', $result);
    }

    /**
     * @testdox getDateFormat returns custom value of date + time empty string is provided as parameter
    */
    public function testGetDateFormatReturnsCustomValueWhenEmptyString()
    {
        // Given
        $this->getCustomMockedData();

        // When
        $result = DateFormat::getDateFormat('');

        // Then
        $this->assertEquals('date time', $result);
    }

    /**
     * @testdox getDateFormat returns custom value of date, when date is provided as parameter
    */
    public function testGetDateFormatReturnsCustomDate()
    {
        // Given
        $this->getCustomMockedData();

        // When
        $result = DateFormat::getDateFormat('date');

        // Then
        $this->assertEquals('date', $result);
    }

    /**
     * @testdox getDateFormat returns custom value of time, when time is provided as parameter
    */
    public function testGetDateFormatReturnsCustomTime()
    {
        // Given
        $this->getCustomMockedData();

        // When
        $result = DateFormat::getDateFormat('time');

        // Then
        $this->assertEquals('time', $result);
    }

    /**
     * @testdox getDateFormat returns custom value of date + time, when date-time is provided as parameter
    */
    public function testGetDateFormatReturnsCustomDateTime()
    {
        // Given
        $this->getCustomMockedData();

        // When
        $result = DateFormat::getDateFormat('date-time');

        // Then
        $this->assertEquals('date time', $result);
    }

    /**
     * Mocked data
    */
    private function getCustomMockedData()
    {
        WP_Mock::userFunction('get_option', [
            'times'  => 2,
            'return' => 'date'
        ]);

        WP_Mock::userFunction('get_option', [
            'times'  => 2,
            'return' => 'time'
        ]);
    }

    /**
     * Mocked data
    */
    private function getDefaultMockedData()
    {
        WP_Mock::userFunction('get_option', [
            'times'  => 1,
            'return' => false
        ]);

        WP_Mock::userFunction('get_option', [
            'times'  => 1,
            'return' => false
        ]);
    }
}
