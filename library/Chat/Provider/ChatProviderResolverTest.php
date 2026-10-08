<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ChatProviderResolverTest extends TestCase
{
    #[TestDox('resolve() returns AI provider when provider_type is missing')]
    public function testResolveReturnsAiProviderWhenProviderTypeIsMissing(): void
    {
        $aiProvider = new class implements ChatProviderInterface {
            public function validateAssistantConfig(array $assistant): ?\WP_Error
            {
                return null;
            }

            public function registerSseStream(\WP_REST_Request $request, array $assistant, array $params, RedactionResult $redaction): void
            {
            }
        };

        $resolver = new ChatProviderResolver($aiProvider);

        static::assertSame($aiProvider, $resolver->resolve([]));
    }

    #[TestDox('resolve() returns AI provider when provider_type is ai regardless of casing')]
    public function testResolveReturnsAiProviderWhenProviderTypeIsAi(): void
    {
        $aiProvider = new class implements ChatProviderInterface {
            public function validateAssistantConfig(array $assistant): ?\WP_Error
            {
                return null;
            }

            public function registerSseStream(\WP_REST_Request $request, array $assistant, array $params, RedactionResult $redaction): void
            {
            }
        };

        $resolver = new ChatProviderResolver($aiProvider);

        static::assertSame($aiProvider, $resolver->resolve(['provider_type' => 'AI']));
    }

    #[TestDox('resolve() returns WP_Error for unsupported provider type')]
    public function testResolveReturnsErrorForUnsupportedProviderType(): void
    {
        $aiProvider = new class implements ChatProviderInterface {
            public function validateAssistantConfig(array $assistant): ?\WP_Error
            {
                return null;
            }

            public function registerSseStream(\WP_REST_Request $request, array $assistant, array $params, RedactionResult $redaction): void
            {
            }
        };

        $resolver = new ChatProviderResolver($aiProvider);
        $resolved = $resolver->resolve(['provider_type' => 'puzzel']);

        static::assertInstanceOf(\WP_Error::class, $resolved);
    }
}
