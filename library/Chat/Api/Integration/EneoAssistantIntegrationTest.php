<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Integration;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class EneoAssistantIntegrationTest extends TestCase
{
    #[TestDox('validateAssistantConfig() returns error when server_url is missing')]
    public function testValidateAssistantConfigReturnsErrorWhenServerUrlIsMissing(): void
    {
        $integration = new EneoAssistantIntegration();

        $error = $integration->validateAssistantConfig([
            'api_key' => 'api-key',
            'assistant_id' => 'assistant-id',
        ]);

        static::assertInstanceOf(\WP_Error::class, $error);
    }

    #[TestDox('validateAssistantConfig() returns error when api_key is missing')]
    public function testValidateAssistantConfigReturnsErrorWhenApiKeyIsMissing(): void
    {
        $integration = new EneoAssistantIntegration();

        $error = $integration->validateAssistantConfig([
            'server_url' => 'https://example.com',
            'assistant_id' => 'assistant-id',
        ]);

        static::assertInstanceOf(\WP_Error::class, $error);
    }

    #[TestDox('validateAssistantConfig() returns error when assistant_id is missing')]
    public function testValidateAssistantConfigReturnsErrorWhenAssistantIdIsMissing(): void
    {
        $integration = new EneoAssistantIntegration();

        $error = $integration->validateAssistantConfig([
            'server_url' => 'https://example.com',
            'api_key' => 'api-key',
        ]);

        static::assertInstanceOf(\WP_Error::class, $error);
    }

    #[TestDox('validateAssistantConfig() returns null when config is complete')]
    public function testValidateAssistantConfigReturnsNullWhenConfigIsComplete(): void
    {
        $integration = new EneoAssistantIntegration();

        $error = $integration->validateAssistantConfig([
            'server_url' => 'https://example.com',
            'api_key' => 'api-key',
            'assistant_id' => 'assistant-id',
        ]);

        static::assertNull($error);
    }
}
