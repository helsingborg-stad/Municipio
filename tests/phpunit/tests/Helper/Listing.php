<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Listing;
use WP_Mock;

/**
 * Class ListingTest
 * @runTestsInSeparateProcesses
 * @group wp_mock
 */
class ListingTest extends TestCase
{
    /**
     * @testdox createListingItem returns false if empty string is provided
    */
    public function testCreateListingItemReturnsFalseIfEmptyString()
    {
        // When
        $result = Listing::createListingItem("");

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox createListingItem returns array with default values if not empty string is provided.
    */
    public function testCreateListingItemReturnsArrayWithDefaultValuesIfHasStringValue()
    {
        // When
        $result = Listing::createListingItem("test");

        // Then
        $this->assertEquals('test', $result['label']);
        $this->assertEquals('', $result['href']);
        $this->assertNotEmpty($result['icon']['size']);
    }

    /**
     * @testdox createListingItem returns array with default values
    */
    public function testCreateListingItemReturnsArrayWithDefaultValues()
    {
        // When
        $result = Listing::createListingItem("test");

        // Then
        $this->assertEquals('test', $result['label']);
        $this->assertEquals('', $result['href']);
        $this->assertNotEmpty($result['icon']['size']);
    }


    /**
     * @testdox createListingItem returns array with custom values and always set icon size to md
    */
    public function testCreateListingItemReturnsArrayWithCustomValues()
    {
        // When
        $result = Listing::createListingItem("test", 'https://test.test', ['icon' => 'test', 'size' => 'sm']);

        // Then
        $this->assertEquals('test', $result['label']);
        $this->assertEquals('https://test.test', $result['href']);
        $this->assertEquals('md', $result['icon']['size']);
        $this->assertEquals('test', $result['icon']['icon']);
    }

    /**
     * @testdox getTermsWithIcon returns false if empty.
    */
    public function testGetTermsWithIconReturnsFalseIfEmpty()
    {
        // When
        $result = Listing::getTermsWithIcon([]);

        // Then
        $this->assertFalse($result);
    }

     /**
     * @testdox getTermsWithIcon returns an array with a term objects.
    */
    public function testGetTermsWithIconReturnsArray()
    {
        // Given
        $termObject          = new \stdClass();
        $termObject->term_id = 1;
        $termObject->name    = 'test';

        WP_Mock::userFunction('get_term')->andReturn($termObject);
        // When
        $result = Listing::getTermsWithIcon([1]);

        // Then
        $this->assertEquals(1, $result[0]->term_id);
        $this->assertEquals('test', $result[0]->name);
        $this->assertFalse($result[0]->icon);
    }
}
