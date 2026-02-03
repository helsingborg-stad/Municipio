<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;

/**
 * Tests for AsyncAttributesReconstructor
 *
 * Verifies that minimal async attributes can be reconstructed into full data.
 */
class AsyncAttributesReconstructorTest extends TestCase
{
    public function testReconstructCreatesFullDataFromMinimalAttributes(): void
    {
        // ARRANGE: Minimal attributes from async request (URL parameters)
        $minimalAttributes = [
            'postType' => 'post',
            'queryVarsPrefix' => 'archive_',
            'archivePropsKey' => 'archivePost',
        ];

        // ACT: Reconstruct full data
        // Note: In a real test environment, we'd need to mock WordPress functions
        // For now, we test the structure of the returned data
        $reconstructed = AsyncAttributesReconstructor::reconstruct($minimalAttributes, null);

        // ASSERT: Full data structure is created
        $this->assertIsArray($reconstructed);
        $this->assertArrayHasKey('postType', $reconstructed);
        $this->assertEquals('post', $reconstructed['postType']);
        $this->assertArrayHasKey('queryVarsPrefix', $reconstructed);
        $this->assertEquals('archive_', $reconstructed['queryVarsPrefix']);

        // Backend-queried data is present
        $this->assertArrayHasKey('wpTaxonomies', $reconstructed);
        $this->assertArrayHasKey('customizer', $reconstructed);
        $this->assertArrayHasKey('archiveProps', $reconstructed);
        $this->assertArrayHasKey('displayArchiveLoop', $reconstructed);
    }

    public function testEnrichMergesReconstructedDataWithOriginal(): void
    {
        // ARRANGE: Attributes with some additional data
        $attributes = [
            'postType' => 'post',
            'queryVarsPrefix' => 'archive_',
            'archivePropsKey' => 'archivePost',
            'numberOfColumns' => 3,
            'dateFormat' => 'date-time',
        ];

        // ACT: Enrich with reconstructed data
        $enriched = AsyncAttributesReconstructor::enrich($attributes, null);

        // ASSERT: Original attributes preserved
        $this->assertEquals(3, $enriched['numberOfColumns']);
        $this->assertEquals('date-time', $enriched['dateFormat']);

        // Reconstructed data added
        $this->assertArrayHasKey('wpTaxonomies', $enriched);
        $this->assertArrayHasKey('customizer', $enriched);
        $this->assertArrayHasKey('archiveProps', $enriched);
    }

    public function testReconstructHandlesDefaultValues(): void
    {
        // ARRANGE: Minimal attributes without optional values
        $minimalAttributes = [];

        // ACT: Reconstruct with defaults
        $reconstructed = AsyncAttributesReconstructor::reconstruct($minimalAttributes, null);

        // ASSERT: Defaults are used
        $this->assertEquals('page', $reconstructed['postType'], 'Default postType should be page');
        $this->assertEquals('archive_', $reconstructed['queryVarsPrefix'], 'Default queryVarsPrefix');
        $this->assertFalse($reconstructed['archiveProps'], 'archiveProps should be false when key not found');
    }
}
