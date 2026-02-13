<?php

namespace Municipio\Archive\AsyncAttributesProvider;

use Municipio\Controller\Archive\AppearanceConfigFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Integration test for Archive async attributes
 *
 * Tests the complete flow from Archive controller to REST API endpoint
 */
class ArchiveAsyncAttributesIntegrationTest extends TestCase
{
    private function createMockWpService(): FakeWpService
    {
        return new FakeWpService([
            'getThemeMod' => 10, // Default posts per page
            'getTerms' => [],
            'getPostTypeArchiveLink' => false, // Return false for archive links in tests
            'removeQueryArg' => '', // Return empty string for query arg removal
        ]);
    }

    private function createProvider(string $postType, object $archiveProps): AsyncAttributesProviderInterface
    {
        $factory = new AsyncAttributesProviderFactory(
            new AppearanceConfigFactory(),
        );
        return $factory->createForArchive(
            $postType,
            $archiveProps,
            $this->createMockWpService(),
            [], // Empty wp_taxonomies for tests
        );
    }

    /**
     * Test that async attributes are compatible with block renderer
     */
    public function testAsyncAttributesAreBlockRendererCompatible()
    {
        $archiveProps = (object) [
            'dateField' => 'post_date',
            'dateFormat' => 'date',
            'style' => 'list',
            'numberOfColumns' => 2,
            'postPropertiesToDisplay' => ['post_title', 'post_date'],
            'taxonomiesToDisplay' => ['category'],
            'displayFeaturedImage' => true,
            'readingTime' => false,
        ];

        $provider = $this->createProvider('news', $archiveProps);
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
        $provider = $this->createProvider('post', (object) []);
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
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('postTypeProvider')]
    public function testDifferentPostTypesGenerateCorrectAttributes(string $postType)
    {
        $provider = $this->createProvider($postType, (object) []);
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
            $provider = $this->createProvider('post', $archiveProps);
            $attributes = $provider->getAttributes();

            $this->assertSame(
                $expectedBlockDesign,
                $attributes['design'],
                "Failed asserting that archive style '{$archiveStyle}' maps to block design '{$expectedBlockDesign}'",
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

        $provider = $this->createProvider('post', $archiveProps);
        $attributes = $provider->getAttributes();

        // Should not include arbitrary properties
        $this->assertArrayNotHasKey('someUnusedProperty', $attributes);
        $this->assertArrayNotHasKey('anotherUnusedProperty', $attributes);

        // Should have the expected keys (in any order)
        $expectedKeys = [
            'postType',
            'queryVarsPrefix',
            'dateSource',
            'dateFormat',
            'design',
            'numberOfColumns',
            'postPropertiesToDisplay',
            'taxonomiesEnabledForFiltering',
            'taxonomiesToDisplay',
            'displayFeaturedImage',
            'displayReadingTime',
            'textSearchEnabled',
            'dateFilterEnabled',
            'postsPerPage',
            'orderBy',
            'order',
        ];

        $actualKeys = array_keys($attributes);
        sort($expectedKeys);
        sort($actualKeys);
        $this->assertSame($expectedKeys, $actualKeys);
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

        $provider = $this->createProvider('post', $archiveProps);
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
