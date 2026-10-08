<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class RequestParamConversationApiProviderFactoryTest extends TestCase
{
    #[TestDox('createProvider() returns mapped provider when known provider id is supplied')]
    public function testCreateProviderReturnsMappedProviderWhenKnownProviderIsSupplied(): void
    {
        $defaultProvider = $this->createFakeProvider();
        $mappedProvider = $this->createFakeProvider();

        $factory = new RequestParamConversationApiProviderFactory(
            defaultProvider: $defaultProvider,
            providerMap: ['ai' => $mappedProvider],
            providerParamName: 'provider',
        );

        static::assertSame($mappedProvider, $factory->createProvider(['provider' => 'ai']));
    }

    #[TestDox('createProvider() falls back to default provider for unknown provider id')]
    public function testCreateProviderFallsBackToDefaultProviderForUnknownProviderId(): void
    {
        $defaultProvider = $this->createFakeProvider();
        $mappedProvider = $this->createFakeProvider();

        $factory = new RequestParamConversationApiProviderFactory(
            defaultProvider: $defaultProvider,
            providerMap: ['ai' => $mappedProvider],
            providerParamName: 'provider',
        );

        static::assertSame($defaultProvider, $factory->createProvider(['provider' => 'future-provider']));
    }

    #[TestDox('createProvider() normalizes provider id casing and whitespace')]
    public function testCreateProviderNormalizesProviderIdCasingAndWhitespace(): void
    {
        $defaultProvider = $this->createFakeProvider();
        $mappedProvider = $this->createFakeProvider();

        $factory = new RequestParamConversationApiProviderFactory(
            defaultProvider: $defaultProvider,
            providerMap: ['ai' => $mappedProvider],
            providerParamName: 'provider',
        );

        static::assertSame($mappedProvider, $factory->createProvider(['provider' => '  AI  ']));
    }

    #[TestDox('createProvider() returns default provider when provider parameter is missing')]
    public function testCreateProviderReturnsDefaultProviderWhenProviderParameterIsMissing(): void
    {
        $defaultProvider = $this->createFakeProvider();

        $factory = new RequestParamConversationApiProviderFactory(
            defaultProvider: $defaultProvider,
            providerMap: [],
            providerParamName: 'provider',
        );

        static::assertSame($defaultProvider, $factory->createProvider(['assistant_name' => 'Ava']));
    }

    private function createFakeProvider(): ConversationApiProviderInterface
    {
        return new class implements ConversationApiProviderInterface {
            public function createProviderRequest(array $requestParams, RedactionResult $redaction): mixed
            {
                return new ProviderRequest('https://example.test/chat', 'key', []);
            }

            public function streamResponse(ProviderRequest $providerRequest): void
            {
            }
        };
    }
}
