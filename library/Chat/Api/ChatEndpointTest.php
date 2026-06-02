<?php

namespace Municipio\Chat\Api;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\Exception\PIIRedactionException;
use Municipio\Chat\PIIRedactor\Passthrough\PassthroughPIIRedactor;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use Override;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\RegisterRestRoute;
use WpService\Implementations\FakeWpService;

class ChatEndpointTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactor(), static::createWpService());

        $this->assertInstanceOf(ChatEndpoint::class, $endpoint);
    }

    #[TestDox('handleRegisterRestRoute() returns true')]
    public function testHandleRegisterRestRouteCanBeCalled(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactor(), static::createWpService());

        $this->assertTrue($endpoint->handleRegisterRestRoute());
    }

    #[TestDox('handleRequest() returns a WP_Error when no message parameter is provided')]
    public function testHandleRequestReturnsErrorWhenMessageIsMissing(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest([]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the message parameter is empty')]
    public function testHandleRequestReturnsErrorWhenMessageIsEmpty(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest(['message' => '']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when no matching assistant is configured')]
    public function testHandleRequestReturnsErrorWhenAssistantNotFound(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'unknown-id',
            'chat_assistants' => [
                ['id' => 'other-id', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = new ChatEndpoint($config, $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the matched assistant is missing server_url')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingServerUrl(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_assistants' => [
                ['id' => 'a1', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = new ChatEndpoint($config, $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the matched assistant is missing api_key')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingApiKey(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_assistants' => [
                ['id' => 'a1', 'server_url' => 'https://x', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = new ChatEndpoint($config, $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the matched assistant is missing assistant_id')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingAssistantId(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_assistants' => [
                ['id' => 'a1', 'server_url' => 'https://x', 'api_key' => 'k'],
            ],
        ]);

        $endpoint = new ChatEndpoint($config, $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the PII redactor throws')]
    public function testHandleRequestReturnsErrorWhenRedactorThrows(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_assistants' => [
                ['id' => 'a1', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $throwingRedactor = new class implements PIIRedactorInterface {
            public function extractAndRedactPII(string $input): RedactionResult
            {
                throw new PIIRedactionException('Presidio unavailable');
            }
        };

        $endpoint = new ChatEndpoint($config, $throwingRedactor, static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() resolves the assistant by the explicit assistant_id parameter when provided')]
    public function testHandleRequestPrefersExplicitAssistantIdParameter(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'default-id',
            'chat_assistants' => [
                ['id' => 'default-id', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
                ['id' => 'explicit-id'],
            ],
        ]);

        $endpoint = new ChatEndpoint($config, $this->getPIIRedactor(), static::createWpService());
        $request = $this->createRequest([
            'message' => 'Hello',
            'assistant_id' => 'explicit-id',
        ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_Error::class, $response);
    }

    private function createRequest(array $params): \WP_REST_Request
    {
        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_params')->willReturn($params);

        return $request;
    }

    private function getConfig(array $fields = []): ChatConfigInterface
    {
        $acfService = new FakeAcfService([
            'getField' => function (string $selector) use ($fields) {
                return $fields[$selector] ?? null;
            },
        ]);

        $wpService = new FakeWpService(['determineLocale' => 'en_US']);

        return new ChatConfig($wpService, $acfService);
    }

    private function getPIIRedactor(): PIIRedactorInterface
    {
        return new PassthroughPIIRedactor();
    }

    private static function createWpService(): RegisterRestRoute
    {
        return new class implements RegisterRestRoute {
            
            public function registerRestRoute(string $routeNamespace, string $route, array $args = [], bool $override = false): bool
            {
                return true;
            }
        };
    }
}
