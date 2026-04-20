<?php

namespace Municipio\Chat;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\PIIRedactor\NullPIIRedactor;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\register_rest_route')) {
    function register_rest_route($namespace, $route, $args = [], $override = false): bool
    {
        return true;
    }
}

if (!function_exists(__NAMESPACE__ . '\rest_ensure_response')) {
    function rest_ensure_response($response): \WP_REST_Response
    {
        return new class ($response) extends \WP_REST_Response {
            public function __construct($data = null)
            {
                $this->data = $data;
            }
        };
    }
}

if (!function_exists(__NAMESPACE__ . '\sanitize_text_field')) {
    function sanitize_text_field($str): string
    {
        return (string)$str;
    }
}

if (!function_exists(__NAMESPACE__ . '\__')) {
    function __($text, $domain = 'default'): string
    {
        return $text;
    }
}

class ChatEndpointTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $endpoint = new ChatEndpoint($this->getAcfService(), $this->getPIIRedactor());

        $this->assertInstanceOf(ChatEndpoint::class, $endpoint);
    }

    #[TestDox('handleRegisterRestRoute() returns true')]
    public function testHandleRegisterRestRouteReturnsTrue(): void
    {
        $endpoint = new ChatEndpoint($this->getAcfService(), $this->getPIIRedactor());

        $this->assertTrue($endpoint->handleRegisterRestRoute());
    }

    #[TestDox('handleRequest() returns an error response when no message parameter is provided')]
    public function testHandleRequestReturnsErrorWhenMessageIsMissing(): void
    {
        $endpoint = new ChatEndpoint($this->getAcfService(), $this->getPIIRedactor());
        $request  = $this->createRequest([]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertIsArray($response->data);
        $this->assertArrayHasKey('error', $response->data);
    }

    #[TestDox('handleRequest() returns an error response when the message parameter is empty')]
    public function testHandleRequestReturnsErrorWhenMessageIsEmpty(): void
    {
        $endpoint = new ChatEndpoint($this->getAcfService(), $this->getPIIRedactor());
        $request  = $this->createRequest(['message' => '']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertArrayHasKey('error', $response->data);
    }

    #[TestDox('handleRequest() returns an error response when no matching assistant is configured')]
    public function testHandleRequestReturnsErrorWhenAssistantNotFound(): void
    {
        $acfService = $this->getAcfService([
            'chat_default_assistant' => 'unknown-id',
            'chat_assistants'        => [
                ['id' => 'other-id', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = new ChatEndpoint($acfService, $this->getPIIRedactor());
        $request  = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertArrayHasKey('error', $response->data);
    }

    #[TestDox('handleRequest() returns an error response when the matched assistant is missing server_url')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingServerUrl(): void
    {
        $acfService = $this->getAcfService([
            'chat_default_assistant' => 'a1',
            'chat_assistants'        => [
                ['id' => 'a1', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = new ChatEndpoint($acfService, $this->getPIIRedactor());
        $request  = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertArrayHasKey('error', $response->data);
    }

    #[TestDox('handleRequest() returns an error response when the matched assistant is missing api_key')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingApiKey(): void
    {
        $acfService = $this->getAcfService([
            'chat_default_assistant' => 'a1',
            'chat_assistants'        => [
                ['id' => 'a1', 'server_url' => 'https://x', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = new ChatEndpoint($acfService, $this->getPIIRedactor());
        $request  = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertArrayHasKey('error', $response->data);
    }

    #[TestDox('handleRequest() returns an error response when the matched assistant is missing assistant_id')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingAssistantId(): void
    {
        $acfService = $this->getAcfService([
            'chat_default_assistant' => 'a1',
            'chat_assistants'        => [
                ['id' => 'a1', 'server_url' => 'https://x', 'api_key' => 'k'],
            ],
        ]);

        $endpoint = new ChatEndpoint($acfService, $this->getPIIRedactor());
        $request  = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertArrayHasKey('error', $response->data);
    }

    #[TestDox('handleRequest() resolves the assistant by the explicit assistant_id parameter when provided')]
    public function testHandleRequestPrefersExplicitAssistantIdParameter(): void
    {
        $acfService = $this->getAcfService([
            'chat_default_assistant' => 'default-id',
            'chat_assistants'        => [
                ['id' => 'default-id', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
                ['id' => 'explicit-id'],
            ],
        ]);

        $endpoint = new ChatEndpoint($acfService, $this->getPIIRedactor());
        $request  = $this->createRequest([
            'message'      => 'Hello',
            'assistant_id' => 'explicit-id',
        ]);

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertArrayHasKey('error', $response->data);
    }

    private function createRequest(array $params): \WP_REST_Request
    {
        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_params')->willReturn($params);

        return $request;
    }

    private function getAcfService(array $fields = []): FakeAcfService
    {
        return new FakeAcfService([
            'getField' => function (string $selector) use ($fields) {
                return $fields[$selector] ?? null;
            },
        ]);
    }

    private function getPIIRedactor(): PIIRedactorInterface
    {
        return new NullPIIRedactor();
    }
}
