<?php

declare(strict_types=1);

namespace Municipio\Chat\Api;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Api\Providers\AiConversationApiProvider;
use Municipio\Chat\Api\Providers\ConversationApiProviderFactoryInterface;
use Municipio\Chat\Api\Providers\ConversationApiProviderInterface;
use Municipio\Chat\Api\Providers\DefaultConversationApiProviderFactory;
use Municipio\Chat\Api\Providers\ProviderRequest;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\Exception\PIIRedactionException;
use Municipio\Chat\PIIRedactor\Passthrough\PassthroughPIIRedactor;
use Municipio\Chat\PIIRedactor\PIIRedactorFactoryInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\RegisterRestRoute;
use WpService\Contracts\RestEnsureResponse;
use WpService\Contracts\SanitizeTextField;
use WpService\Contracts\__;
use WpService\Implementations\FakeWpService;

class MockPIIRedactorFactory implements PIIRedactorFactoryInterface
{
    public function __construct(
        private $mockRedactor = null,
    ) {}

    public function create(ChatConfigInterface $config): PIIRedactorInterface
    {
        return $this->mockRedactor ?? new PassthroughPIIRedactor();
    }
}

class FakeConversationApiProvider implements ConversationApiProviderInterface
{
    public function __construct(
        private mixed $providerRequest = null,
    ) {
    }

    public function createProviderRequest(array $requestParams, RedactionResult $redaction): mixed
    {
        if (is_object($this->providerRequest) && is_a($this->providerRequest, 'WP_Error')) {
            return $this->providerRequest;
        }

        return $this->providerRequest ?? new ProviderRequest(
            chatUrl: 'https://example.com/chat',
            apiKey: 'key',
            body: ['question' => $redaction->redactedText, 'stream' => true],
        );
    }

    public function streamResponse(ProviderRequest $providerRequest): void
    {
        // Intentionally empty for unit tests.
    }
}

class FakeConversationApiProviderFactory implements ConversationApiProviderFactoryInterface
{
    public function __construct(
        private ConversationApiProviderInterface $provider,
    ) {
    }

    public function createProvider(array $requestParams): ConversationApiProviderInterface
    {
        return $this->provider;
    }
}

class ChatEndpointTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), $this->getConversationProviderFactory(), static::createWpService());

        static::assertInstanceOf(ChatEndpoint::class, $endpoint);
    }

    #[TestDox('handleRegisterRestRoute() returns true')]
    public function testHandleRegisterRestRouteCanBeCalled(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), $this->getConversationProviderFactory(), static::createWpService());

        static::assertTrue($endpoint->handleRegisterRestRoute());
    }

    #[TestDox('handleRequest() returns a WP_Error when no message parameter is provided')]
    public function testHandleRequestReturnsErrorWhenMessageIsMissing(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), $this->getConversationProviderFactory(), static::createWpService());
        $request = $this->createRequest([]);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when the message parameter is empty')]
    public function testHandleRequestReturnsErrorWhenMessageIsEmpty(): void
    {
        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), $this->getConversationProviderFactory(), static::createWpService());
        $request = $this->createRequest(['message' => '']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when no matching assistant is configured')]
    public function testHandleRequestReturnsErrorWhenAssistantNotFound(): void
    {
        $provider = new FakeConversationApiProvider($this->createWpError('chat_assistant_not_found', 'Assistant not found.', ['status' => 404]));

        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), new FakeConversationApiProviderFactory($provider), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when provider reports incomplete assistant configuration')]
    public function testHandleRequestReturnsErrorWhenProviderReportsIncompleteAssistantConfiguration(): void
    {
        $provider = new FakeConversationApiProvider($this->createWpError('chat_assistant_incomplete', 'Assistant configuration is incomplete.', ['status' => 500]));

        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), new FakeConversationApiProviderFactory($provider), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when provider reports incomplete assistant API credentials')]
    public function testHandleRequestReturnsErrorWhenProviderReportsIncompleteAssistantApiCredentials(): void
    {
        $provider = new FakeConversationApiProvider($this->createWpError('chat_assistant_incomplete', 'Assistant configuration is incomplete.', ['status' => 500]));

        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), new FakeConversationApiProviderFactory($provider), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() returns a WP_Error when provider reports missing assistant identifier')]
    public function testHandleRequestReturnsErrorWhenProviderReportsMissingAssistantIdentifier(): void
    {
        $provider = new FakeConversationApiProvider($this->createWpError('chat_assistant_incomplete', 'Assistant configuration is incomplete.', ['status' => 500]));

        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), new FakeConversationApiProviderFactory($provider), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
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

        $throwingRedactorFactory = new MockPIIRedactorFactory($throwingRedactor);

        $endpoint = new ChatEndpoint($config, $throwingRedactorFactory, $this->getConversationProviderFactory(), static::createWpService());
        $request = $this->createRequest(['message' => 'Hello']);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() returns provider resolution errors regardless of extra assistant_id parameter')]
    public function testHandleRequestReturnsProviderResolutionErrorWithExtraAssistantIdParameter(): void
    {
        $provider = new FakeConversationApiProvider($this->createWpError('chat_assistant_not_found', 'Assistant not found.', ['status' => 404]));

        $endpoint = new ChatEndpoint($this->getConfig(), $this->getPIIRedactorFactory(), new FakeConversationApiProviderFactory($provider), static::createWpService());
        $request = $this->createRequest([
            'message' => 'Hello',
            'assistant_id' => 'explicit-id',
        ]);

        $response = $endpoint->handleRequest($request);

        static::assertInstanceOf('WP_Error', $response);
    }

    private function createRequest(array $params): object
    {
        $request = $this->createMock('WP_REST_Request');
        $request->method('get_params')->willReturn($params);

        return $request;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createWpError(string $code, string $message, array $data): object
    {
        $errorClass = 'WP_Error';
        return new $errorClass($code, $message, $data);
    }

    private function getConfig(array $fields = []): ChatConfigInterface
    {
        $acfService = new FakeAcfService([
            'getField' => static fn(string $selector) => $fields[$selector] ?? null,
        ]);

        $wpService = new FakeWpService(['determineLocale' => 'en_US']);

        return new ChatConfig($wpService, $acfService);
    }

    private function getPIIRedactorFactory(): PIIRedactorFactoryInterface
    {
        return new MockPIIRedactorFactory();
    }

    private function getConversationProvider(?ChatConfigInterface $config = null): ConversationApiProviderInterface
    {
        return new AiConversationApiProvider(
            $config ?? $this->getConfig(),
            new FakeWpService([
                '__' => static fn(string $text) => $text,
            ]),
        );
    }

    private function getConversationProviderFactory(?ChatConfigInterface $config = null): ConversationApiProviderFactoryInterface
    {
        return new DefaultConversationApiProviderFactory($this->getConversationProvider($config));
    }

    private static function createWpService(): RegisterRestRoute&AddFilter&SanitizeTextField&RestEnsureResponse&__
    {
        return new FakeWpService([
            'registerRestRoute' => true,
            'addFilter' => true,
            'sanitizeTextField' => static fn(string $value): string => $value,
            'restEnsureResponse' => static function (mixed $response): object {
                $responseClass = 'WP_REST_Response';
                return new $responseClass($response);
            },
            '__' => static fn(string $text): string => $text,
        ]);
    }
}
