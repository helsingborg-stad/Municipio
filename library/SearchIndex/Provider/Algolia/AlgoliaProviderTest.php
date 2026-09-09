<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use Algolia\AlgoliaSearch\Api\SearchClient;
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
        $client = $this->createMock(SearchClient::class);
        $client->expects($this->once())
            ->method('deleteBy')
            ->with('municipio-content', ['filters' => 'origin_site_url:"https://current.example.test"']);
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
        $clientProperty = new \ReflectionProperty($provider, 'client');
        $clientProperty->setValue($provider, $client);

        $provider->clearObjects();
    }

    /**
     * Verify that resetting the index deletes it entirely.
     */
    public function testResetIndexDeletesTheIndex(): void
    {
        $index = $this->createMock(SearchClient::class);
        $index->expects($this->once())->method('deleteIndex')->with('municipio-content');
        $provider = new AlgoliaProvider(
            new FakeWpService([
                'getCurrentUserId' => 0,
                'applyFilters' => static fn(string $hookName, mixed $value): mixed => $value,
            ]),
            'application-id',
            implode('-', ['algolia', 'admin', 'key']),
            'municipio-content',
        );
        $indexProperty = new \ReflectionProperty($provider, 'client');
        $indexProperty->setValue($provider, $index);

        $provider->resetIndex();
    }
}