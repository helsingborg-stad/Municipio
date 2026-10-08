<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AiConversationApiProviderTest extends TestCase
{
    #[TestDox('createProviderRequest() returns error when assistant cannot be resolved')]
    public function testCreateProviderRequestReturnsErrorWhenAssistantCannotBeResolved(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'Default Missing',
            'chat_assistants' => [],
        ]);

        $provider = new AiConversationApiProvider($config, $this->createWpService());

        $response = $provider->createProviderRequest(
            ['assistant_name' => 'Unknown'],
            $this->createRedactionResult('hello'),
        );

        $this->assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('createProviderRequest() includes session_id when one is provided')]
    public function testCreateProviderRequestUsesSessionIdWhenPresent(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'Ava',
            'chat_assistants' => [
                [
                    'name' => 'Ava',
                    'server_url' => 'https://example.test/chat',
                    'api_key' => 'secret',
                    'assistant_id' => 'assistant-1',
                ],
            ],
        ]);

        $provider = new AiConversationApiProvider($config, $this->createWpService());

        $response = $provider->createProviderRequest(
            [
                'assistant_name' => 'Ava',
                'session_id' => 'session-123',
            ],
            $this->createRedactionResult('redacted question'),
        );

        $this->assertInstanceOf(ProviderRequest::class, $response);
        $this->assertSame('https://example.test/chat', $response->chatUrl);
        $this->assertSame('secret', $response->apiKey);
        $this->assertSame('redacted question', $response->body['question']);
        $this->assertSame(true, $response->body['stream']);
        $this->assertSame('session-123', $response->body['session_id']);
        $this->assertArrayNotHasKey('assistant_id', $response->body);
    }

    #[TestDox('createProviderRequest() uses assistant_id when session_id is absent')]
    public function testCreateProviderRequestUsesAssistantIdWhenSessionIdIsAbsent(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'Ava',
            'chat_assistants' => [
                [
                    'name' => 'Ava',
                    'server_url' => 'https://example.test/chat',
                    'api_key' => 'secret',
                    'assistant_id' => 'assistant-1',
                ],
            ],
        ]);

        $provider = new AiConversationApiProvider($config, $this->createWpService());

        $response = $provider->createProviderRequest(
            [
                'assistant_name' => 'Ava',
            ],
            $this->createRedactionResult('redacted question'),
        );

        $this->assertInstanceOf(ProviderRequest::class, $response);
        $this->assertSame('assistant-1', $response->body['assistant_id']);
        $this->assertArrayNotHasKey('session_id', $response->body);
    }

    /**
     * @param array<string, mixed> $fields
     */
    private function getConfig(array $fields = []): ChatConfigInterface
    {
        $acfService = new FakeAcfService([
            'getField' => static fn(string $selector) => $fields[$selector] ?? null,
        ]);

        $wpService = new FakeWpService([
            'determineLocale' => 'en_US',
        ]);

        return new ChatConfig($wpService, $acfService);
    }

    private function createWpService(): FakeWpService
    {
        return new FakeWpService([
            '__' => static fn(string $text) => $text,
        ]);
    }

    private function createRedactionResult(string $redactedText): RedactionResult
    {
        $redactionResult = new RedactionResult();
        $redactionResult->redactedText = $redactedText;

        return $redactionResult;
    }
}
