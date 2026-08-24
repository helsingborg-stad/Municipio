<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Algolia;

use AcfService\Implementations\FakeAcfService;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests Algolia's browser-safe integration configuration.
 */
class AlgoliaProviderRegistrarTest extends TestCase
{
    /**
     * Verify that only the public Algolia connection settings are exposed.
     */
    public function testAddsBrowserConfigWhenPublicKeyIsConfigured(): void
    {
        $values = [
            'search_index_provider' => 'algolia',
            'search_index_algolia_application_id' => 'application-id',
            'search_index_algolia_public_api_key' => implode('-', ['public', 'key']),
            'search_index_name' => 'municipio-content',
        ];
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $values[$selector] ?? '',
        ]));
        $registrar = new AlgoliaProviderRegistrar(new FakeWpService(), $config);
        $publicApiKey = implode('-', ['public', 'key']);

        static::assertSame([
            'type' => 'algolia',
            'applicationId' => 'application-id',
            'apiKey' => $publicApiKey,
            'indexName' => 'municipio-content',
        ], $registrar->addBrowserConfig([]));
    }
}