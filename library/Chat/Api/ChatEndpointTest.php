<?php

declare(strict_types=1);

namespace Municipio\Chat\Api;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Api\Integration\ChatAssistantIntegrationInterface;
use Municipio\Chat\Api\Integration\EneoAssistantIntegration;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\Exception\PIIRedactionException;
use Municipio\Chat\PIIRedactor\Passthrough\PassthroughPIIRedactor;
use Municipio\Chat\PIIRedactor\PIIRedactorFactoryInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\RegisterRestRoute;
use WpService\Implementations\FakeWpService;

/** Provide a string result where the WordPress test stub returns null. */
function sanitize_text_field(string $value): string
{
    return $value;
}

class MockPIIRedactorFactory implements PIIRedactorFactoryInterface
{
    public int $createCount = 0;

    public function __construct(
        private $mockRedactor = null,
    ) {}

    public function create(ChatConfigInterface $config): PIIRedactorInterface
    {
        $this->createCount++;
        return $this->mockRedactor ?? new PassthroughPIIRedactor();
    }
}

class ChatEndpointTest extends TestCase
{
    #[TestDox('handleRequest() does not call integration when message is missing')]
    public function testHandleRequestDoesNotCallIntegrationWhenMessageIsMissing(): void
    {
        $integration = new FakeChatAssistantIntegration();
        $endpoint = $this->createEndpoint(integration: $integration);
        $request = $this->createRequest([]);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
        static::assertFalse($integration->wasRegisterResponseHandlerCalled);
    }

    #[TestDox('handleRequest() does not call integration when pii redaction fails')]
    public function testHandleRequestDoesNotCallIntegrationWhenPiiRedactionFails(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'a1', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $throwingRedactor = new class implements PIIRedactorInterface {
            public function extractAndRedactPII(string $input): RedactionResult
            {
                throw new PIIRedactionException('Presidio unavailable');
            }
        };

        $integration = new FakeChatAssistantIntegration();
        $throwingRedactorFactory = new MockPIIRedactorFactory($throwingRedactor);
        $endpoint = $this->createEndpoint($config, $throwingRedactorFactory, $integration);
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
        static::assertFalse($integration->wasRegisterResponseHandlerCalled);
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $endpoint = $this->createEndpoint();

        static::assertInstanceOf(ChatEndpoint::class, $endpoint);
    }

    #[TestDox('handleRegisterRestRoute() returns true')]
    public function testHandleRegisterRestRouteCanBeCalled(): void
    {
        $endpoint = $this->createEndpoint();

        static::assertTrue($endpoint->handleRegisterRestRoute());
    }

    #[TestDox('handleRequest() returns a WP_Error when no message parameter is provided')]
    public function testHandleRequestReturnsErrorWhenMessageIsMissing(): void
    {
        $endpoint = $this->createEndpoint();
        $request = $this->createRequest([]);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the message parameter is empty')]
    public function testHandleRequestReturnsErrorWhenMessageIsEmpty(): void
    {
        $endpoint = $this->createEndpoint();
        $request = $this->createRequest(['message' => '']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when no matching assistant is configured')]
    public function testHandleRequestReturnsErrorWhenAssistantNotFound(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'unknown-id',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'other-id', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = $this->createEndpoint($config);
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the matched assistant is missing server_url')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingServerUrl(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'a1', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = $this->createEndpoint($config);
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the matched assistant is missing api_key')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingApiKey(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'a1', 'server_url' => 'https://x', 'assistant_id' => 'a'],
            ],
        ]);

        $endpoint = $this->createEndpoint($config);
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the matched assistant is missing assistant_id')]
    public function testHandleRequestReturnsErrorWhenAssistantMissingAssistantId(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'a1', 'server_url' => 'https://x', 'api_key' => 'k'],
            ],
        ]);

        $endpoint = $this->createEndpoint($config);
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() denies a restricted default assistant for guests')]
    public function testGuestCannotUseRestrictedDefaultAssistant(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'Private',
            'chat_assistants' => [
                ['name' => 'Private', 'logged_in_only' => 1, 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);
        $redactorFactory = new MockPIIRedactorFactory();
        $endpoint = new ChatEndpoint($config, $redactorFactory, static::createWpService());

        $response = $endpoint->handleRequest($this->createRequest(['message' => 'Hello']));

        static::assertInstanceOf(\WP_Error::class, $response);
        static::assertSame(0, $redactorFactory->createCount);
    }

    #[TestDox('handleRequest() denies an explicitly requested restricted assistant for guests')]
    public function testGuestCannotUseRestrictedNamedAssistant(): void
    {
        $config = $this->getConfig([
            'chat_assistants' => [
                ['name' => 'Private', 'logged_in_only' => 1, 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);
        $redactorFactory = new MockPIIRedactorFactory();
        $endpoint = new ChatEndpoint($config, $redactorFactory, static::createWpService());

        $response = $endpoint->handleRequest($this->createRequest(['message' => 'Hello', 'assistant_name' => 'Private']));

        static::assertInstanceOf(\WP_Error::class, $response);
        static::assertSame(0, $redactorFactory->createCount);
    }

    #[TestDox('handleRequest() lets a logged-in user reach redaction for a restricted assistant')]
    public function testLoggedInUserCanUseRestrictedAssistant(): void
    {
        $config = $this->getConfig([
            'chat_assistants' => [
                ['name' => 'Private', 'logged_in_only' => 1, 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ], true);
        $redactor = new class implements PIIRedactorInterface {
            public function extractAndRedactPII(string $input): RedactionResult
            {
                throw new PIIRedactionException('Redaction reached');
            }
        };
        $redactorFactory = new MockPIIRedactorFactory($redactor);
        $endpoint = new ChatEndpoint($config, $redactorFactory, static::createWpService());

        $response = $endpoint->handleRequest($this->createRequest(['message' => 'Hello', 'assistant_name' => 'Private']));

        static::assertInstanceOf(\WP_Error::class, $response);
        static::assertSame(1, $redactorFactory->createCount);
    }

    #[TestDox('handleRequest() returns a WP_Error when the PII redactor throws')]
    public function testHandleRequestReturnsErrorWhenRedactorThrows(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'a1',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'a1', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
            ],
        ]);

        $throwingRedactor = new class implements PIIRedactorInterface {
            public function extractAndRedactPII(string $input): RedactionResult
            {
                throw new PIIRedactionException('Presidio unavailable');
            }
        };

        $throwingRedactorFactory = new MockPIIRedactorFactory($throwingRedactor);

        $endpoint = $this->createEndpoint($config, $throwingRedactorFactory);
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    #[TestDox('handleRequest() resolves assistant from assistant_name parameter when provided')]
    public function testHandleRequestResolvesAssistantFromAssistantNameParameter(): void
    {
        $config = $this->getConfig([
            'chat_default_assistant' => 'default-name',
            'chat_integrations' => [
                ['acf_fc_layout' => 'eneo', 'name' => 'default-name', 'server_url' => 'https://x', 'api_key' => 'k', 'assistant_id' => 'a'],
                ['acf_fc_layout' => 'eneo', 'name' => 'explicit-name'],
            ],
        ]);

        $endpoint = $this->createEndpoint($config);
        $request = $this->createRequest([
            'message' => 'Hello',
            'assistant_name' => 'explicit-name',
        ]);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf(\WP_Error::class, $response);
    }

    private function createRequest(array $params): \WP_REST_Request
    {
        $request = $this->createMock(\WP_REST_Request::class);
        $request->method('get_params')->willReturn($params);

        return $request;
    }

    private function getConfig(array $fields = [], bool $isUserLoggedIn = false): ChatConfigInterface
    {
        $acfService = new FakeAcfService([
            'getField' => static fn(string $selector) => $fields[$selector] ?? null,
        ]);

        $wpService = new FakeWpService(['determineLocale' => 'en_US', 'isUserLoggedIn' => $isUserLoggedIn]);

        return new ChatConfig($wpService, $acfService);
    }

    private function getPIIRedactorFactory(): PIIRedactorFactoryInterface
    {
        return new MockPIIRedactorFactory();
    }

    private function createEndpoint(
        ?ChatConfigInterface $config = null,
        ?PIIRedactorFactoryInterface $piiRedactorFactory = null,
        ?ChatAssistantIntegrationInterface $integration = null,
    ): ChatEndpoint {
        return new ChatEndpoint(
            $config ?? $this->getConfig(),
            $piiRedactorFactory ?? $this->getPIIRedactorFactory(),
            $integration ?? new EneoAssistantIntegration(),
            static::createWpService(),
        );
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

class FakeChatAssistantIntegration implements ChatAssistantIntegrationInterface
{
    public bool $wasRegisterResponseHandlerCalled = false;

    public function validateAssistantConfig(array $assistant): ?\WP_Error
    {
        return null;
    }

    public function registerResponseHandler(
        \WP_REST_Request $request,
        array $assistant,
        array $params,
        RedactionResult $redaction,
    ): void {
        $this->wasRegisterResponseHandlerCalled = true;
    }
}
