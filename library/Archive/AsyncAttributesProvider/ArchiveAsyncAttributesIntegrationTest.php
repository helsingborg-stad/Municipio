<?php

namespace Municipio\Archive\AsyncAttributesProvider;

use PHPUnit\Framework\TestCase;

/**
 * Integration test for Archive async attributes
 *
 * Tests the complete flow from Archive controller to REST API endpoint
 */
class ArchiveAsyncAttributesIntegrationTest extends TestCase
{
    /**
     * Test that async attributes are compatible with block renderer
     */
    public function testAsyncAttributesAreBlockRendererCompatible()
    {
        $archiveProps = (object) [
            'dateField' => 'post_date',
            'date_format' => 'date',
            'style' => 'list',
            'numberOfColumns' => 2,
            'postPropertiesToDisplay' => ['post_title', 'post_date'],
            'taxonomiesToDisplay' => ['category'],
            'featured_image' => true,
            'reading_time' => false,
        ];

        $provider = new ArchiveAsyncAttributesProvider('news', $archiveProps);
        $attributes = $provider->getAttributes();

        // Verify all required block renderer attributes are present
        $this->assertArrayHasKey('postType', $attributes);
        $this->assertArrayHasKey('dateSource', $attributes);
        $this->assertArrayHasKey('dateFormat', $attributes);
        $this->assertArrayHasKey('design', $attributes);
        $this->assertArrayHasKey('numberOfColumns', $attributes);
        $this->assertArrayHasKey('postPropertiesToDisplay', $attributes);
        $this->assertArrayHasKey('taxonomiesToDisplay', $attributes);
        $this->assertArrayHasKey('displayFeaturedImage', $attributes);
        $this->assertArrayHasKey('displayReadingTime', $attributes);

        // Verify postType is singular string, not array
        $this->assertIsString($attributes['postType']);
        $this->assertSame('news', $attributes['postType']);
    }

    /**
     * Test that async attributes match expected format for REST API
     */
    public function testAsyncAttributesMatchRestApiFormat()
    {
        $provider = new ArchiveAsyncAttributesProvider('post', (object) []);
        $attributes = $provider->getAttributes();

        // Test JSON encoding/decoding
        $json = json_encode($attributes);
        $this->assertNotFalse($json);

        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('postType', $decoded);
        $this->assertSame('post', $decoded['postType']);
    }

    /**
     * Test that different post types generate correct attributes
     *
     * @dataProvider postTypeProvider
     */
    public function testDifferentPostTypesGenerateCorrectAttributes(string $postType)
    {
        $provider = new ArchiveAsyncAttributesProvider($postType, (object) []);
        $attributes = $provider->getAttributes();

        $this->assertSame($postType, $attributes['postType']);
    }

    /**
     * Data provider for post types
     */
    public static function postTypeProvider(): array
    {
        return [
            'post' => ['post'],
            'page' => ['page'],
            'news' => ['news'],
            'custom_post_type' => ['custom_post_type'],
            'with-dashes' => ['with-dashes'],
        ];
    }

    /**
     * Test that design mapping works correctly for all archive styles
     */
    public function testDesignMappingForAllStyles()
    {
        $styles = [
            'cards' => 'card',
            'collection' => 'collection',
            'compressed' => 'compressed',
            'grid' => 'block',
            'list' => 'table',
            'newsitem' => 'newsitem',
            'schema' => 'schema',
        ];

        foreach ($styles as $archiveStyle => $expectedBlockDesign) {
            $archiveProps = (object) ['style' => $archiveStyle];
            $provider = new ArchiveAsyncAttributesProvider('post', $archiveProps);
            $attributes = $provider->getAttributes();

            $this->assertSame(
                $expectedBlockDesign,
                $attributes['design'],
                "Failed asserting that archive style '{$archiveStyle}' maps to block design '{$expectedBlockDesign}'"
            );
        }
    }

    /**
     * Test that attributes are minimal and don't include unnecessary data
     */
    public function testAttributesAreMinimal()
    {
        $archiveProps = (object) [
            'dateField' => 'post_date',
            'style' => 'cards',
            'someUnusedProperty' => 'should not be included',
            'anotherUnusedProperty' => ['not', 'needed'],
        ];

        $provider = new ArchiveAsyncAttributesProvider('post', $archiveProps);
        $attributes = $provider->getAttributes();

        // Should not include arbitrary properties
        $this->assertArrayNotHasKey('someUnusedProperty', $attributes);
        $this->assertArrayNotHasKey('anotherUnusedProperty', $attributes);

        // Should only have the expected keys
        $expectedKeys = [
            'postType',
            'dateSource',
            'dateFormat',
            'design',
            'numberOfColumns',
            'postPropertiesToDisplay',
            'taxonomiesToDisplay',
            'displayFeaturedImage',
            'displayReadingTime',
        ];

        $this->assertSame($expectedKeys, array_keys($attributes));
    }

    /**
     * Test that empty arrays are preserved (not converted to null or other types)
     */
    public function testEmptyArraysArePreserved()
    {
        $archiveProps = (object) [
            'postPropertiesToDisplay' => [],
            'taxonomiesToDisplay' => [],
        ];

        $provider = new ArchiveAsyncAttributesProvider('post', $archiveProps);
        $attributes = $provider->getAttributes();

        $this->assertIsArray($attributes['postPropertiesToDisplay']);
        $this->assertEmpty($attributes['postPropertiesToDisplay']);
        $this->assertIsArray($attributes['taxonomiesToDisplay']);
        $this->assertEmpty($attributes['taxonomiesToDisplay']);

        // Verify they remain arrays after JSON encoding/decoding
        $json = json_encode($attributes);
        $decoded = json_decode($json, true);

        $this->assertIsArray($decoded['postPropertiesToDisplay']);
        $this->assertEmpty($decoded['postPropertiesToDisplay']);
        $this->assertIsArray($decoded['taxonomiesToDisplay']);
        $this->assertEmpty($decoded['taxonomiesToDisplay']);
    }
}
