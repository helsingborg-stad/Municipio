<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Config;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\Constant\FakeConstant;
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
            'search_index_algolia_index_name' => 'municipio-content',
        ];
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $values[$selector] ?? '',
        ]));

        static::assertTrue($config->isConfigured());
        static::assertSame('application-id', $config->algoliaApplicationId());
        static::assertSame($apiKey, $config->algoliaApiKey());
        static::assertSame('municipio-content', $config->algoliaIndexName());
    }

    /**
     * Verify that Typesense requires its API URL and server-side API key.
     */
    public function testIsConfiguredWithTypesenseCredentials(): void
    {
        $values = [
            'search_index_provider' => 'typesense',
            'search_index_typesense_api_url' => 'https://typesense.example.test/',
            'search_index_typesense_api_key' => implode('-', ['typesense', 'server', 'key']),
            'search_index_typesense_public_api_key' => implode('-', ['typesense', 'public', 'key']),
            'search_index_typesense_collection_name' => 'municipio-content',
        ];
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $values[$selector] ?? '',
        ]));

        static::assertTrue($config->isConfigured());
        static::assertTrue($config->isProviderConfigured('typesense'));
        static::assertFalse($config->isProviderConfigured('algolia'));
        static::assertSame('https://typesense.example.test', $config->typesenseApiUrl());
        static::assertSame('municipio-content', $config->typesenseCollectionName());
    }

    /**
     * Verify legacy plugin constants remain supported during migration.
     */
    public function testSupportsLegacyPluginConstants(): void
    {
        $constantService = new FakeConstant([
            'ALGOLIAINDEX_APPLICATION_ID' => 'legacy-application-id',
            'ALGOLIAINDEX_API_KEY' => implode('-', ['legacy', 'algolia', 'key']),
            'ALGOLIAINDEX_PUBLIC_API_KEY' => implode('-', ['legacy', 'algolia', 'public', 'key']),
            'ALGOLIAINDEX_INDEX_NAME' => 'legacy-algolia-index',
            'TYPESENSEINDEX_API_URL' => 'https://typesense.example.test/',
            'TYPESENSEINDEX_API_KEY' => implode('-', ['legacy', 'typesense', 'key']),
            'TYPESENSEINDEX_PUBLIC_API_KEY' => implode('-', ['legacy', 'typesense', 'public', 'key']),
            'TYPESENSEINDEX_COLLECTION_NAME' => 'legacy-typesense-collection',
        ]);

        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $selector === 'search_index_provider' ? 'typesense' : '',
        ]), $constantService);

        static::assertSame('legacy-application-id', $config->algoliaApplicationId());
        static::assertSame('legacy-algolia-index', $config->algoliaIndexName());
        static::assertTrue($config->isConfigured());
        static::assertSame('https://typesense.example.test', $config->typesenseApiUrl());
        static::assertSame('legacy-typesense-collection', $config->typesenseCollectionName());
    }

    /**
     * Verify new environment constants take precedence over legacy names.
     */
    public function testPrefersNewEnvironmentConstants(): void
    {
        $constantService = new FakeConstant([
            'SEARCH_INDEX_ALGOLIA_APPLICATION_ID' => 'new-application-id',
            'ALGOLIAINDEX_APPLICATION_ID' => 'legacy-application-id',
            'SEARCH_INDEX_ALGOLIA_API_KEY' => implode('-', ['new', 'algolia', 'key']),
            'ALGOLIAINDEX_API_KEY' => implode('-', ['legacy', 'algolia', 'key']),
        ]);

        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(): string => '',
        ]), $constantService);

        static::assertSame('new-application-id', $config->algoliaApplicationId());
        static::assertTrue($config->isConfigured());
    }
}