<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Typesense;

use AcfService\Implementations\FakeAcfService;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests Typesense provider registration and browser integration configuration.
 */
class TypesenseProviderRegistrarTest extends TestCase
{
    /**
     * Verify that the Typesense provider factory is registered.
     */
    public function testRegistersTypesenseProvider(): void
    {
        $registrar = $this->createRegistrar();
        $providers = $registrar->registerProvider([]);

        static::assertArrayHasKey('typesense', $providers);
        static::assertIsCallable($providers['typesense']);
        static::assertInstanceOf(TypesenseProvider::class, $providers['typesense']());
    }

    /**
     * Verify that public Typesense settings are exposed to browser integrations.
     */
    public function testAddsBrowserConfigAndCspDomainForTypesense(): void
    {
        $registrar = $this->createRegistrar();
        $browserConfig = $registrar->addBrowserConfig([]);
        $directives = $registrar->addCspDomain([]);

        static::assertSame('typesense', $browserConfig['type']);
        static::assertSame('typesense.example.test', $browserConfig['host']);
        static::assertSame(443, $browserConfig['port']);
        static::assertSame('typesense-public-key', $browserConfig['apiKey']);
        static::assertSame(['https://typesense.example.test'], $directives['connect-src']);
    }

    /**
     * Verify that the CSP origin preserves an explicit port without URL credentials or paths.
     */
    public function testAddsSanitizedCspOriginWithExplicitPort(): void
    {
        $registrar = $this->createRegistrar([
            'search_index_typesense_api_url' => 'HTTPS://user:password@typesense.example.test:8108/api?key=value',
        ]);

        $directives = $registrar->addCspDomain([]);

        static::assertSame(['https://typesense.example.test:8108'], $directives['connect-src']);
    }

    /**
     * Verify that unsupported Typesense URL schemes are not added to CSP.
     */
    public function testDoesNotAddCspOriginForUnsupportedScheme(): void
    {
        $registrar = $this->createRegistrar([
            'search_index_typesense_api_url' => 'ftp://typesense.example.test:8108',
        ]);
        $directives = ['connect-src' => ["'self'"]];

        static::assertSame($directives, $registrar->addCspDomain($directives));
    }

    /**
     * Verify that Typesense does not broaden CSP when another provider is selected.
     */
    public function testDoesNotAddCspOriginWhenTypesenseIsNotSelected(): void
    {
        $registrar = $this->createRegistrar([
            'search_index_provider' => 'algolia',
        ]);
        $directives = ['connect-src' => ["'self'"]];

        static::assertSame($directives, $registrar->addCspDomain($directives));
    }

    /**
     * Create the subject using configured Typesense settings.
     *
     * @param array<string, string> $overrides
     */
    private function createRegistrar(array $overrides = []): TypesenseProviderRegistrar
    {
        $values = array_merge([
            'search_index_provider' => 'typesense',
            'search_index_typesense_api_url' => 'https://typesense.example.test',
            'search_index_typesense_api_key' => implode('-', ['typesense', 'server', 'key']),
            'search_index_typesense_public_api_key' => implode('-', ['typesense', 'public', 'key']),
            'search_index_typesense_collection_name' => 'municipio-content',
        ], $overrides);
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $values[$selector] ?? '',
        ]));

        return new TypesenseProviderRegistrar(new FakeWpService(), $config);
    }
}