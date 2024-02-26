<?php

namespace Municipio\Tests\Helper;

use Mockery;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Location;

/**
 * Class HtmlTest
 */
class LocationTest extends TestCase
{
    /**
     * @testdox addLocationDataToPosts returns posts array with location property added to posts with location field.
    */
    public function testAddLocationDataToPostsAddsTheLocationKey()
    {
        // Given
        WP_Mock::userFunction('get_field', [
            'return' => ['lat' => '1', 'lng' => '2'],
            'times'  => 1,
        ]);

        WP_Mock::userFunction('get_field', [
            'return' => null,
            'times'  => 1,
        ]);

        // When
        $result = Location::addLocationDataToPosts([
            $this->getMockedPost(),
            $this->getMockedPost(),
        ]);

        // Then
        $this->assertIsArray($result);
        $this->assertObjectHasProperty('location', $result[0]);
        $this->assertObjectNotHasProperty('location', $result[1]);
    }

    /**
     * @testdox addLocationDataToPosts skips getting data if no post id is available.
    */
    public function testAddLocationDataToPostsSkipsGettingDataIfNoPostId()
    {
        // Given
        WP_Mock::userFunction('get_field', [
            'return' => ['lat' => '1', 'lng' => '2'],
            'times'  => 0,
        ]);

        // When
        $result = Location::addLocationDataToPosts([
            $this->getMockedPost(['id' => null]),
        ]);

        // Then
        $this->assertIsArray($result);
        $this->assertObjectNotHasProperty('location', $result[0]);
    }

    /**
     * @testdox createMapMarkers returns an array with pins and skips post that has no location data.
    */
    public function testCreateMapMarkersReturnsPinsArrayAndSkipsPostWithNoLocationData()
    {
        // Given
        $this->getUserFunctions();

        // When
        $result = Location::createMapMarkers([
            $this->getMockedPost(['id' => '1', 'location' => ['lat' => '1', 'lng' => '1']]),
            $this->getMockedPost(['id' => '2', 'location' => ['lat' => '2', 'lng' => '2']]),
            $this->getMockedPost(['id' => '3']),
        ]);

        // Then
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /**
     * @testdox createMapMarkers returns an array with pins and skips post that has no location data.
    */
    public function testCreateMapMarkersReturnsEmptyArrayIfNoPosts()
    {
        // Given
        $this->getUserFunctions();

        // When
        $result = Location::createMapMarkers([]);

        // Then
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox createMapMarker returns empty array if the Post is missing the location key.
    */
    public function testCreateMapMarkerReturnsEmptyArrayWhenPostMissingLocation()
    {
        // When
        $result = Location::createMapMarker($this->getMockedpost());

        // Then
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox createMapMarker returns a pin when Post contains a location.
    */
    public function testCreateMapMarkerReturnsAPinWhenPostContainsALocation()
    {
        // Given
        $this->getUserFunctions();

        // When
        $result = Location::createMapMarker($this->getMockedpost(['location' => ['lat' => '1', 'lng' => '1']]));

        // Then
        $this->assertArrayHasKey('lat', $result);
        $this->assertArrayHasKey('lng', $result);
        $this->assertArrayHasKey('tooltip', $result);
        $this->assertArrayHasKey('directions', $result['tooltip']);
        $this->assertArrayHasKey('url', $result['tooltip']['directions']);
        $this->assertArrayHasKey('label', $result['tooltip']['directions']);
        $this->assertArrayNotHasKey('icon', $result);
    }

    /**
     * @testdox createMapMarker returns a pin with the icon key set.
    */
    public function testCreateMapMarkerReturnsPinWithIconKeySet()
    {
        // Given
        $this->getUserFunctions();

        // When
        $result = Location::createMapMarker(
            $this->getMockedpost([
                'location' => ['lat' => '1', 'lng' => '1'],
                'termIcon' => 'test'
            ])
        );

        // Then
        $this->assertEquals('test', $result['icon']);
    }

    /**
     * @testdox filterPostsWithLocationData returns posts that contains location data.
    */
    public function testFilterPostsWithLocationDataReturnsFilteredPosts()
    {
        // Given
        $this->getUserFunctions();

        // When
        $result = Location::filterPostsWithLocationData(
            [
                $this->getMockedpost(['location' => ['lat' => '1', 'lng' => '1']]),
                $this->getMockedpost()
            ]
        );

        // Then
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    /**
     * @testdox filterPostsWithLocationData returns empty array if no posts containing location data.
    */
    public function testFilterPostsWithLocationDataReturnsEmptyArray()
    {
        // Given
        $this->getUserFunctions();

        // When
        $result = Location::filterPostsWithLocationData(
            [
                $this->getMockedpost(),
                $this->getMockedpost()
            ]
        );

        // Then
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * User functions
    */
    private function getUserFunctions()
    {
        WP_Mock::userFunction('get_permalink', [
            'return' => 'https://test.test'
        ]);
    }

    /**
     * Mock posts.
    */
    private function getMockedPost(array $args = [])
    {
        return $this->mockPost(array_merge([
            'id'           => 1,
            'post_title'   => 'test',
            'post_content' => 'test',
            'post_excerpt' => 'Test',
            'permalink'    => 'https://url.url',
            'post_type'    => 'test',
            'terms'        => ['test' => 'test']
        ], $args));
    }
}
