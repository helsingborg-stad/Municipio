<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultConversationApiProviderFactoryTest extends TestCase
{
    #[TestDox('createProvider() returns the configured default provider')]
    public function testCreateProviderReturnsConfiguredDefaultProvider(): void
    {
        $provider = new class implements ConversationApiProviderInterface {
            public function createProviderRequest(array $requestParams, RedactionResult $redaction): mixed
            {
                return new ProviderRequest('https://example.test/chat', 'key', []);
            }

            public function streamResponse(ProviderRequest $providerRequest): void
            {
            }
        };

        $factory = new DefaultConversationApiProviderFactory($provider);

        static::assertSame($provider, $factory->createProvider(['assistant_name' => 'Ava']));
    }
}
