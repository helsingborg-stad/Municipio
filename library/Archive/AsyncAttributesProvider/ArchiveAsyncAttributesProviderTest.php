<?php

namespace Municipio\Archive\AsyncAttributesProvider;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetThemeMod;

/**
 * Test case for ArchiveAsyncAttributesProvider
 *
 * @coversDefaultClass \Municipio\Archive\AsyncAttributesProvider\ArchiveAsyncAttributesProvider
 */
class ArchiveAsyncAttributesProviderTest extends TestCase
{
    private function createMockWpService(int $postsPerPage = 10): GetThemeMod
    {
        $mock = $this->createMock(GetThemeMod::class);
        $mock->method('getThemeMod')->willReturn($postsPerPage);
        return $mock;
    }

    /**
     * @covers ::__construct
     * @covers ::getAttributes
     * @covers ::buildAttributes
     * @covers ::getPostsPerPage
     * @covers ::mapOrder
     */
    public function testGetAttributesWithEmptyArchiveProps()
    {
        $wpService = $this->createMockWpService(10);
        $provider = new ArchiveAsyncAttributesProvider('news', (object) [], $wpService);
        $attributes = $provider->getAttributes();

        $this->assertIsArray($attributes);
        $this->assertSame('news', $attributes['postType']);
        $this->assertSame('post_date', $attributes['dateSource']);
        $this->assertSame('date', $attributes['dateFormat']);
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
     * @covers ::getPostsPerPage
     * @covers ::mapOrder
     */
    public function testGetAttributesWithFullArchiveProps()
    {
        $wpService = $this->createMockWpService(20);
        $archiveProps = (object) [
            'dateField' => 'modified',
            'date_format' => 'datetime',
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

        $provider = new ArchiveAsyncAttributesProvider('post', $archiveProps, $wpService);
        $attributes = $provider->getAttributes();

        $this->assertSame('post', $attributes['postType']);
        $this->assertSame('modified', $attributes['dateSource']);
        $this->assertSame('datetime', $attributes['dateFormat']);
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
     * @covers ::mapDesign
     * @dataProvider designMappingProvider
     */
    public function testDesignMapping(string $archiveStyle, string $expectedDesign)
    {
        $wpService = $this->createMockWpService();
        $archiveProps = (object) ['style' => $archiveStyle];
        $provider = new ArchiveAsyncAttributesProvider('post', $archiveProps, $wpService);
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
        $provider = new ArchiveAsyncAttributesProvider('custom_post_type', (object) [], $wpService);
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

        $provider = new ArchiveAsyncAttributesProvider('news', $archiveProps, $wpService);
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

        $provider = new ArchiveAsyncAttributesProvider('page', $archiveProps, $wpService);
        $attributes = $provider->getAttributes();

        // Custom values
        $this->assertSame('custom_date', $attributes['dateSource']);
        $this->assertSame(['title'], $attributes['postPropertiesToDisplay']);

        // Default values for missing props
        $this->assertSame('date', $attributes['dateFormat']);
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
        $provider = new ArchiveAsyncAttributesProvider('post', (object) [], $wpService);
        $attributes1 = $provider->getAttributes();
        $attributes2 = $provider->getAttributes();

        $this->assertSame($attributes1, $attributes2);
    }
}
