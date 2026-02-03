<?php

namespace Municipio\Archive\AsyncAttributesProvider;

use Municipio\Controller\Archive\AppearanceConfigFactory;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetTerms;
use WpService\Contracts\GetThemeMod;

/**
 * Test case for ArchiveAsyncAttributesProvider with factory-based initialization
 *
 * Tests use the AsyncAttributesProviderFactory to create provider instances,
 * ensuring tests reflect production usage patterns.
 *
 * @coversDefaultClass \Municipio\Archive\AsyncAttributesProvider\ArchiveAsyncAttributesProvider
 */
class ArchiveAsyncAttributesProviderTest extends TestCase
{
    private function createMockWpService(int $postsPerPage = 10): GetThemeMod&GetTerms
    {
        // Create mock for intersection type GetThemeMod&GetTerms
        $mock = $this->getMockBuilder(GetThemeMod::class)
            ->addMethods(['getTerms']) // Add GetTerms methods
            ->getMock();

        $mock->method('getThemeMod')->willReturn($postsPerPage);
        $mock->method('getTerms')->willReturn([]);

        return $mock;
    }

    private function createProvider(string $postType, object $archiveProps, GetThemeMod&GetTerms $wpService): AsyncAttributesProviderInterface
    {
        $factory = new AsyncAttributesProviderFactory(
            new AppearanceConfigFactory()
        );
        return $factory->createForArchive(
            $postType,
            $archiveProps,
            $wpService,
            [] // Empty wp_taxonomies for tests
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getAttributes
     * @covers ::buildAttributes
     */
    public function testGetAttributesWithEmptyArchiveProps()
    {
        $wpService = $this->createMockWpService(10);
        $provider = $this->createProvider('news', (object) [], $wpService);
        $attributes = $provider->getAttributes();

        $this->assertIsArray($attributes);
        $this->assertSame('news', $attributes['postType']);
        $this->assertSame('archive_', $attributes['queryVarsPrefix']);
        $this->assertSame('post_date', $attributes['dateSource']);
        $this->assertSame('date-time', $attributes['dateFormat']); // Default changed to date-time
        $this->assertSame('card', $attributes['design']);
        $this->assertSame(3, $attributes['numberOfColumns']);
        $this->assertSame([], $attributes['postPropertiesToDisplay']);
        $this->assertSame([], $attributes['taxonomiesToDisplay']);
        $this->assertFalse($attributes['displayFeaturedImage']);
        $this->assertFalse($attributes['displayReadingTime']);
        // Filter settings
        $this->assertFalse($attributes['textSearchEnabled']);
        $this->assertFalse($attributes['dateFilterEnabled']);
        // Pagination and ordering
        $this->assertSame(10, $attributes['postsPerPage']);
        $this->assertSame('post_date', $attributes['orderBy']);
        $this->assertSame('desc', $attributes['order']);
    }

    /**
     * @covers ::getAttributes
     * @covers ::buildAttributes
     */
    public function testGetAttributesWithFullArchiveProps()
    {
        $wpService = $this->createMockWpService(20);
        $archiveProps = (object) [
            'dateField' => 'modified',
            'dateFormat' => 'date-time',
            'style' => 'list',
            'numberOfColumns' => 2,
            'postPropertiesToDisplay' => ['post_title', 'post_date'],
            'taxonomiesToDisplay' => ['category', 'post_tag'],
            'featured_image' => true,
            'reading_time' => true,
            'enabledFilters' => ['text_search', 'date_range'],
            'orderBy' => 'title',
            'orderDirection' => 'ASC',
        ];

        $provider = $this->createProvider('post', $archiveProps, $wpService);
        $attributes = $provider->getAttributes();

        $this->assertSame('post', $attributes['postType']);
        $this->assertSame('modified', $attributes['dateSource']);
        $this->assertSame('date-time', $attributes['dateFormat']);
        $this->assertSame('table', $attributes['design']);
        $this->assertSame(2, $attributes['numberOfColumns']);
        $this->assertSame(['post_title', 'post_date'], $attributes['postPropertiesToDisplay']);
        $this->assertSame(['category', 'post_tag'], $attributes['taxonomiesToDisplay']);
        $this->assertTrue($attributes['displayFeaturedImage']);
        $this->assertTrue($attributes['displayReadingTime']);
        // Filter settings
        $this->assertTrue($attributes['textSearchEnabled']);
        $this->assertTrue($attributes['dateFilterEnabled']);
        // Pagination and ordering
        $this->assertSame(20, $attributes['postsPerPage']);
        $this->assertSame('title', $attributes['orderBy']);
        $this->assertSame('asc', $attributes['order']);
    }

    /**
     * @covers ::getAttributes
     * @dataProvider designMappingProvider
     */
    public function testDesignMapping(string $archiveStyle, string $expectedDesign)
    {
        $wpService = $this->createMockWpService();
        $archiveProps = (object) ['style' => $archiveStyle];
        $provider = $this->createProvider('post', $archiveProps, $wpService);
        $attributes = $provider->getAttributes();

        $this->assertSame($expectedDesign, $attributes['design']);
    }

    /**
     * Data provider for design mapping tests
     */
    public static function designMappingProvider(): array
    {
        return [
            'cards' => ['cards', 'card'],
            'collection' => ['collection', 'collection'],
            'compressed' => ['compressed', 'compressed'],
            'grid' => ['grid', 'block'],
            'list' => ['list', 'table'],
            'newsitem' => ['newsitem', 'newsitem'],
            'schema' => ['schema', 'schema'],
            'unknown' => ['unknown', 'card'],
        ];
    }

    /**
     * @covers ::getAttributes
     */
    public function testPostTypeIsCorrectlySet()
    {
        $wpService = $this->createMockWpService();
        $provider = $this->createProvider('custom_post_type', (object) [], $wpService);
        $attributes = $provider->getAttributes();

        $this->assertSame('custom_post_type', $attributes['postType']);
    }

    /**
     * @covers ::getAttributes
     */
    public function testAttributesAreJsonSerializable()
    {
        $wpService = $this->createMockWpService();
        $archiveProps = (object) [
            'dateField' => 'post_date',
            'style' => 'cards',
            'postPropertiesToDisplay' => ['title', 'date'],
            'taxonomiesToDisplay' => ['category'],
        ];

        $provider = $this->createProvider('news', $archiveProps, $wpService);
        $attributes = $provider->getAttributes();

        $json = json_encode($attributes);
        $this->assertNotFalse($json);

        $decoded = json_decode($json, true);
        $this->assertSame($attributes, $decoded);
    }

    /**
     * @covers ::getAttributes
     */
    public function testPartialArchiveProps()
    {
        $wpService = $this->createMockWpService();
        $archiveProps = (object) [
            'dateField' => 'custom_date',
            'postPropertiesToDisplay' => ['title'],
        ];

        $provider = $this->createProvider('page', $archiveProps, $wpService);
        $attributes = $provider->getAttributes();

        // Custom values
        $this->assertSame('custom_date', $attributes['dateSource']);
        $this->assertSame(['title'], $attributes['postPropertiesToDisplay']);

        // Default values for missing props
        $this->assertSame('date-time', $attributes['dateFormat']); // Default is date-time
        $this->assertSame('card', $attributes['design']);
        $this->assertSame(3, $attributes['numberOfColumns']);
        $this->assertSame([], $attributes['taxonomiesToDisplay']);
        $this->assertFalse($attributes['displayFeaturedImage']);
        $this->assertFalse($attributes['displayReadingTime']);
    }

    /**
     * @covers ::getAttributes
     */
    public function testReturnsSameInstanceOnMultipleCalls()
    {
        $wpService = $this->createMockWpService();
        $provider = $this->createProvider('post', (object) [], $wpService);
        $attributes1 = $provider->getAttributes();
        $attributes2 = $provider->getAttributes();

        $this->assertSame($attributes1, $attributes2);
    }
}
