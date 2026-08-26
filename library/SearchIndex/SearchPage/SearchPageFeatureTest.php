<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage;

use AcfService\Implementations\FakeAcfService;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManager;

/**
 * Tests the interactive SearchIndex search-page integration.
 */
class SearchPageFeatureTest extends TestCase
{
    /**
     * Verify the subfeature registers Municipio's custom search-page mount and assets.
     */
    public function testRegistersSearchPageHooks(): void
    {
        $wpService = $this->createWpService();
        $feature = new SearchPageFeature(
            $wpService,
            new EnqueueManager($wpService),
            $this->createConfig(),
        );

        $feature->addHooks();

        $actions = array_map(static fn(array $arguments): string => $arguments[0], $wpService->methodCalls['addAction']);
        $filters = array_map(static fn(array $arguments): string => $arguments[0], $wpService->methodCalls['addFilter']);
        static::assertContains('wp_enqueue_scripts', $actions);
        static::assertContains('custom_search_page', $actions);
        static::assertContains('Municipio/SearchIndex/BackendSearchActive', $filters);
    }

    /**
     * Verify backend provider search is disabled only on the configured search page.
     */
    public function testDisablesBackendSearchOnInteractiveSearchPage(): void
    {
        $wpService = $this->createWpService();
        $feature = new SearchPageFeature(
            $wpService,
            new EnqueueManager($wpService),
            $this->createConfig(),
        );

        static::assertFalse($feature->disableBackendSearch(true));
    }

    private function createWpService(): FakeWpService
    {
        return new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
            'isAdmin' => false,
            'isSearch' => true,
            '__' => static fn(string $text): string => $text,
            'applyFilters' => static fn(string $hook, mixed $value): mixed => $value,
            'wpCacheGet' => false,
            'wpCacheSet' => true,
        ]);
    }

    private function createConfig(): SearchIndexConfig
    {
        $apiKey = implode('-', ['api', 'key']);
        return new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $field): string => match ($field) {
                'search_index_provider' => 'algolia',
                'search_index_algolia_application_id' => 'application-id',
                'search_index_algolia_api_key' => $apiKey,
                default => '',
            },
        ]));
    }
}