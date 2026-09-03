<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use Algolia\AlgoliaSearch\SearchIndex;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests Algolia provider behavior without a live Algolia service.
 */
class AlgoliaProviderTest extends TestCase
{
    /**
     * Verify that clearing only deletes records originating from the current site.
     */
    public function testClearObjectsFiltersByCurrentSiteUrl(): void
    {
        $index = $this->createMock(SearchIndex::class);
        $index->expects($this->never())->method('clearObjects');
        $index->expects($this->once())
            ->method('deleteBy')
            ->with(['filters' => 'origin_site_url:"https://current.example.test"']);
        $provider = new AlgoliaProvider(
            new FakeWpService([
                'getBloginfo' => 'https://current.example.test',
                'getCurrentUserId' => 0,
                'applyFilters' => static fn(string $hookName, mixed $value): mixed => $value,
            ]),
            'application-id',
            implode('-', ['algolia', 'admin', 'key']),
            'municipio-content',
        );
        $indexProperty = new \ReflectionProperty($provider, 'index');
        $indexProperty->setValue($provider, $index);

        $provider->clearObjects();
    }
}