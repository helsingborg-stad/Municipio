<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;

class AsyncConfigBuilderTest extends TestCase
{
    public function testBuildReturnsOnlySourceAttributes(): void
    {
        // NEW BEHAVIOR: build() returns ONLY source attributes (minimal identifiers)
        // All setter values are ignored in favor of ultra-minimal URL data
        $builder = (new AsyncConfigBuilder())
            ->setSourceAttributes([
                'postType' => 'post',
                'queryVarsPrefix' => 'archive_',
                'archivePropsKey' => 'archivePost',
            ])
            ->setDateSource('post_date')  // These setters are ignored
            ->setDateFormat('date-time')
            ->setNumberOfColumns(3)
            ->setPostsPerPage(10)
            ->setPaginationEnabled(true)
            ->setAsyncId('async_123')
            ->setIsAsync(true);

        $result = $builder->build();

        // Only the 3 minimal identifiers from source attributes
        $this->assertEquals([
            'postType' => 'post',
            'queryVarsPrefix' => 'archive_',
            'archivePropsKey' => 'archivePost',
        ], $result);
    }

    public function testBuilderReturnsEmptyWhenNoSourceAttributes(): void
    {
        // NEW BEHAVIOR: Without source attributes, build() returns empty array
        $builder = new AsyncConfigBuilder();
        $result = $builder->build();

        $this->assertEquals([], $result);
    }

    public function testResetClearsSourceAttributes(): void
    {
        // NEW BEHAVIOR: reset() clears source attributes
        $builder = (new AsyncConfigBuilder())
            ->setSourceAttributes([
                'postType' => 'post',
                'queryVarsPrefix' => 'archive_',
                'archivePropsKey' => 'archivePost',
            ])
            ->setNumberOfColumns(3)
            ->setIsAsync(true);

        $builder->reset();
        $result = $builder->build();

        // After reset, source attributes are empty
        $this->assertEquals([], $result);
    }

    public function testBuilderImplementsFluentInterface(): void
    {
        $builder = new AsyncConfigBuilder();
        $result = $builder->setId('test');

        $this->assertInstanceOf(AsyncConfigBuilderInterface::class, $result);
        $this->assertSame($builder, $result);
    }

    public function testMinimalSourceAttributesArePreserved(): void
    {
        // NEW BEHAVIOR: Only minimal attributes are preserved (allowlist approach)
        $sourceAttributes = [
            'postType' => 'post',
            'queryVarsPrefix' => 'archive_',
            'archivePropsKey' => 'archivePost',
            // These should be filtered out (not in allowlist)
            'archiveProps' => (object)[
                'design' => 'card',
                'textSearchEnabled' => true,
                'dateFilterEnabled' => true,
            ],
            'customizer' => ['theme' => 'dark'],
            'wpTaxonomies' => [],
        ];

        $builder = (new AsyncConfigBuilder())
            ->setSourceAttributes($sourceAttributes)
            ->setId('test_id')
            ->setIsAsync(true);

        $result = $builder->build();

        // Only minimal source attributes should be preserved
        $this->assertArrayHasKey('postType', $result);
        $this->assertEquals('post', $result['postType']);
        $this->assertArrayHasKey('queryVarsPrefix', $result);
        $this->assertEquals('archive_', $result['queryVarsPrefix']);
        $this->assertArrayHasKey('archivePropsKey', $result);
        $this->assertEquals('archivePost', $result['archivePropsKey']);

        // Large objects should NOT be preserved (filtered by allowlist)
        $this->assertArrayNotHasKey('archiveProps', $result, 'archiveProps should be filtered out');
        $this->assertArrayNotHasKey('customizer', $result, 'customizer should be filtered out');
        $this->assertArrayNotHasKey('wpTaxonomies', $result, 'wpTaxonomies should be filtered out');

        // Ultra-minimal: Even explicit settings are NOT in result
        // Only source attributes are returned
        $this->assertArrayNotHasKey('id', $result, 'id not in ultra-minimal result');
        $this->assertArrayNotHasKey('isAsync', $result, 'isAsync not in ultra-minimal result');
    }

    public function testMinimalApproachReducesURLSize(): void
    {
        // NEW BEHAVIOR: Source attributes are filtered to minimal set
        $sourceAttributes = [
            'postType' => 'post',
            'queryVarsPrefix' => 'archive_',
            'archivePropsKey' => 'archivePost',
            // All of this data would make URLs too large, so it's filtered out
            'archiveProps' => (object)[
                'numberOfColumns' => 3,
                'design' => 'compressed',
                'taxonomiesEnabledForFiltering' => [
                    ['taxonomy' => 'category', 'type' => 'multi'],
                ],
                'textSearchEnabled' => true,
                'dateFilterEnabled' => true,
                'dateFrom' => '2024-01-01',
                'dateTo' => '2024-12-31',
            ],
        ];

        $builder = (new AsyncConfigBuilder())
            ->setSourceAttributes($sourceAttributes)
            ->setQueryVarsPrefix('archive_')
            ->setIsAsync(true);

        $result = $builder->build();

        // Only minimal data in result (small URL)
        $this->assertArrayNotHasKey('archiveProps', $result, 'archiveProps filtered to minimize URL size');

        // Only the 3 essential identifiers present
        $this->assertCount(3, $result, 'Should have exactly 3 attributes');
        $this->assertEquals('post', $result['postType']);
        $this->assertEquals('archive_', $result['queryVarsPrefix']);
        $this->assertEquals('archivePost', $result['archivePropsKey']);

        // Backend will reconstruct full config from these identifiers
        // isAsync is NOT in the result anymore
        $this->assertArrayNotHasKey('isAsync', $result);
    }

    public function testExplicitSettingsOverrideSourceAttributes(): void
    {
        $sourceAttributes = [
            'postType' => 'page',
            'queryVarsPrefix' => 'archive_',
        ];

        $builder = (new AsyncConfigBuilder())
            ->setSourceAttributes($sourceAttributes)
            ->setNumberOfColumns(4)  // Explicit setting
            ->setPostType('post');    // Override source attribute

        $result = $builder->build();

        // Ultra-minimal: ONLY source attributes returned, setters ignored
        $this->assertCount(2, $result, 'Should have exactly 2 source attributes');
        $this->assertArrayNotHasKey('numberOfColumns', $result, 'Explicit setter ignored');

        // Source attributes are returned as-is
        $this->assertEquals('page', $result['postType'], 'Original source postType (setPostType ignored)');
        $this->assertEquals('archive_', $result['queryVarsPrefix']);
    }
}
