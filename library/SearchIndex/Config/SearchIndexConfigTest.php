<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Config;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;

/**
 * Tests configuration resolution for the SearchIndex feature.
 */
class SearchIndexConfigTest extends TestCase
{
    /**
     * Verify that incomplete Algolia settings keep the feature disabled.
     */
    public function testIsNotConfiguredWithoutAlgoliaCredentials(): void
    {
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(): string => '',
        ]));

        static::assertSame('algolia', $config->provider());
        static::assertFalse($config->isConfigured());
    }

    /**
     * Verify that settings from ACF configure the Algolia provider.
     */
    public function testIsConfiguredWithAlgoliaCredentials(): void
    {
        $apiKey = implode('-', ['api', 'key']);
        $values = [
            'search_index_provider' => 'algolia',
            'search_index_algolia_application_id' => 'application-id',
            'search_index_algolia_api_key' => $apiKey,
            'search_index_name' => 'municipio-content',
        ];
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $values[$selector] ?? '',
        ]));

        static::assertTrue($config->isConfigured());
        static::assertSame('application-id', $config->algoliaApplicationId());
        static::assertSame($apiKey, $config->algoliaApiKey());
        static::assertSame('municipio-content', $config->indexName());
    }
}