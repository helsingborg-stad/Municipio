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
     * Create the subject using configured Typesense settings.
     */
    private function createRegistrar(): TypesenseProviderRegistrar
    {
        $values = [
            'search_index_provider' => 'typesense',
            'search_index_typesense_api_url' => 'https://typesense.example.test',
            'search_index_typesense_api_key' => implode('-', ['typesense', 'server', 'key']),
            'search_index_typesense_public_api_key' => implode('-', ['typesense', 'public', 'key']),
            'search_index_typesense_collection_name' => 'municipio-content',
        ];
        $config = new SearchIndexConfig(new FakeAcfService([
            'getField' => static fn(string $selector): string => $values[$selector] ?? '',
        ]));

        return new TypesenseProviderRegistrar(new FakeWpService(), $config);
    }
}