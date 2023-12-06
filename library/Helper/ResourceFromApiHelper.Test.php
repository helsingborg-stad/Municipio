<?php

namespace Municipio\Helper;

use WP_UnitTestCase;

class ResourceFromApiHelperTest extends WP_UnitTestCase
{
    public function testGetClosestImageBySizeReturnsExactMatchIfFound()
    {
        // Given
        $size = [300, 300];
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertEquals('medium', $result);
    }

    public function testGetClosestImageBySizeReturnsClosestLargerSizeIfExactMatchNotFound()
    {
        // Given
        $size = [151, 151];
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertEquals('medium', $result);
    }

    public function testGetClosestImageBySizeReturnsClosestMatchWhenOnlyWidthIsProvided()
    {
        // Given
        $size = [151];
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertEquals('medium', $result);
    }
    
    public function testGetClosestImageBySizeReturnsClosestMatchWhenOnlyHeightIsProvided()
    {
        // Given
        $size = [false, 151];
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertEquals('medium', $result);
    }

    public function testGetClosestImageBySizeReturnsMatchByName()
    {
        // Given
        $size = 'large';
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertEquals('large', $result);
    }

    public function testGetClosestImageBySizeReturnsNullIfNoMatchingNameFound()
    {
        // Given
        $size = 'foo';
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertNull($result);
    }

    public function testGetClosestImageBySizeReturnsNullIfNoMatchingLargerSizeFound()
    {
        // Given
        $size = [1025];
        $sizes = $this->getMockedSizes();

        // When
        $result = ResourceFromApiHelper::getClosestImageBySize($size, $sizes);

        // Then
        $this->assertNull($result);
    }

    private function getMockedSizes(): object
    {
        return (object) [
            'thumbnail' => (object) ['width' => 150, 'height' => 150, 'source_url' => 'http://example.com/thumbnail.jpg'],
            'medium' => (object) ['width' => 300, 'height' => 300, 'source_url' => 'http://example.com/medium.jpg'],
            'large' => (object) ['width' => 1024, 'height' => 1024, 'source_url' => 'http://example.com/large.jpg'],
        ];
    }
}
