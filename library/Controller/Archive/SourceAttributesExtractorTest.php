<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;

class SourceAttributesExtractorTest extends TestCase
{
    public function testExtractPreservesArchiveProps(): void
    {
        $sourceData = [
            'archiveProps' => (object)[
                'numberOfColumns' => 3,
                'design' => 'card',
                'dateFilterEnabled' => true,
                'textSearchEnabled' => true,
            ],
            'postType' => 'post',
            'queryVarsPrefix' => 'archive_',
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $this->assertArrayHasKey('sourceAttributes', $result);
        $this->assertArrayHasKey('archiveProps', $result['sourceAttributes']);
        $this->assertEquals(3, $result['sourceAttributes']['archiveProps']->numberOfColumns);
        $this->assertEquals('card', $result['sourceAttributes']['archiveProps']->design);
    }

    public function testExtractFiltersOutClosures(): void
    {
        $sourceData = [
            'postType' => 'post',
            'callback' => fn() => 'test',
            'archiveProps' => (object)['design' => 'card'],
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $this->assertArrayNotHasKey('callback', $result['sourceAttributes']);
        $this->assertArrayHasKey('postType', $result['sourceAttributes']);
        $this->assertArrayHasKey('archiveProps', $result['sourceAttributes']);
    }

    public function testExtractFiltersOutNonSerializableObjects(): void
    {
        $sourceData = [
            'postType' => 'post',
            'wpQuery' => new \stdClass(), // Should be excluded by EXCLUDED_KEYS
            'archiveProps' => (object)['design' => 'card'],
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $this->assertArrayNotHasKey('wpQuery', $result['sourceAttributes']);
        $this->assertArrayHasKey('archiveProps', $result['sourceAttributes']);
    }

    public function testExtractPreservesTaxonomyFilters(): void
    {
        $sourceData = [
            'archiveProps' => (object)[
                'taxonomiesEnabledForFiltering' => [
                    ['taxonomy' => 'category', 'type' => 'multi'],
                    ['taxonomy' => 'post_tag', 'type' => 'single'],
                ],
            ],
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $taxonomies = $result['sourceAttributes']['archiveProps']->taxonomiesEnabledForFiltering;
        $this->assertCount(2, $taxonomies);
        $this->assertEquals('category', $taxonomies[0]['taxonomy']);
        $this->assertEquals('multi', $taxonomies[0]['type']);
    }

    public function testExtractPreservesFilterSettings(): void
    {
        $sourceData = [
            'archiveProps' => (object)[
                'textSearchEnabled' => true,
                'dateFilterEnabled' => true,
                'dateFrom' => '2024-01-01',
                'dateTo' => '2024-12-31',
            ],
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $props = $result['sourceAttributes']['archiveProps'];
        $this->assertTrue($props->textSearchEnabled);
        $this->assertTrue($props->dateFilterEnabled);
        $this->assertEquals('2024-01-01', $props->dateFrom);
        $this->assertEquals('2024-12-31', $props->dateTo);
    }

    public function testExtractPreservesUISettings(): void
    {
        $sourceData = [
            'archiveProps' => (object)[
                'numberOfColumns' => 4,
                'design' => 'compressed',
                'dateFormat' => 'date-time',
                'dateSource' => 'modified_date',
            ],
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $props = $result['sourceAttributes']['archiveProps'];
        $this->assertEquals(4, $props->numberOfColumns);
        $this->assertEquals('compressed', $props->design);
        $this->assertEquals('date-time', $props->dateFormat);
        $this->assertEquals('modified_date', $props->dateSource);
    }

    public function testExtractHandlesNestedArrays(): void
    {
        $sourceData = [
            'archiveProps' => (object)[
                'terms' => [
                    ['taxonomy' => 'category', 'terms' => [1, 2, 3]],
                    ['taxonomy' => 'post_tag', 'terms' => [4, 5]],
                ],
            ],
        ];

        $extractor = new SourceAttributesExtractor($sourceData);
        $result = $extractor->extract();

        $terms = $result['sourceAttributes']['archiveProps']->terms;
        $this->assertCount(2, $terms);
        $this->assertEquals([1, 2, 3], $terms[0]['terms']);
        $this->assertEquals([4, 5], $terms[1]['terms']);
    }
}
