<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AiChatProviderTest extends TestCase
{
    #[TestDox('validateAssistantConfig() returns null for complete assistant config')]
    public function testValidateAssistantConfigReturnsNullForCompleteConfig(): void
    {
        $provider = new AiChatProvider();

        $result = $provider->validateAssistantConfig([
            'server_url' => 'https://example.com/chat',
            'api_key' => 'secret',
            'assistant_id' => 'assistant-1',
        ]);

        static::assertNull($result);
    }

    #[TestDox('validateAssistantConfig() returns WP_Error when server_url is missing')]
    public function testValidateAssistantConfigReturnsErrorWhenServerUrlMissing(): void
    {
        $provider = new AiChatProvider();

        $result = $provider->validateAssistantConfig([
            'api_key' => 'secret',
            'assistant_id' => 'assistant-1',
        ]);

        static::assertInstanceOf(\WP_Error::class, $result);
    }

    #[TestDox('validateAssistantConfig() returns WP_Error when api_key is missing')]
    public function testValidateAssistantConfigReturnsErrorWhenApiKeyMissing(): void
    {
        $provider = new AiChatProvider();

        $result = $provider->validateAssistantConfig([
            'server_url' => 'https://example.com/chat',
            'assistant_id' => 'assistant-1',
        ]);

        static::assertInstanceOf(\WP_Error::class, $result);
    }

    #[TestDox('validateAssistantConfig() returns WP_Error when assistant_id is missing')]
    public function testValidateAssistantConfigReturnsErrorWhenAssistantIdMissing(): void
    {
        $provider = new AiChatProvider();

        $result = $provider->validateAssistantConfig([
            'server_url' => 'https://example.com/chat',
            'api_key' => 'secret',
        ]);

        static::assertInstanceOf(\WP_Error::class, $result);
    }
}
