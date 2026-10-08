<?php

namespace Municipio\Chat\Api;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ChatStatsEndpointTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $wpService = new FakeWpService([
            'registerRestRoute' => true,
            'restEnsureResponse' => fn(array $response) => $this->createWpRestResponse($response),
            '__' => fn(string $string) => $string,
            'getOption' => 0,
            'updateOption' => true,
        ]);
        $this->assertInstanceOf(ChatStatsEndpoint::class, new ChatStatsEndpoint($wpService));
    }

    #[TestDox('handleRegisterRestRoute() can be called')]
    public function testHandleRegisterRestRouteCanBeCalled(): void
    {
        $wpService = new FakeWpService([
            'registerRestRoute' => true,
            'restEnsureResponse' => fn(array $response) => $response,
            '__' => fn(string $string) => $string,
            'getOption' => 0,
            'updateOption' => true,
        ]);
        $endpoint = new ChatStatsEndpoint($wpService);
        $this->assertTrue(method_exists($endpoint, 'handleRegisterRestRoute'));
    }

    #[TestDox('handleRequest() returns a WP_Error for an invalid stat type')]
    public function testHandleRequestReturnsErrorForInvalidType(): void
    {
        $wpService = new FakeWpService([
            'registerRestRoute' => true,
            'restEnsureResponse' => fn(array $response) => $response,
            '__' => fn(string $string) => $string,
            'getOption' => 0,
            'updateOption' => true,
        ]);

        $endpoint = new ChatStatsEndpoint($wpService);
        $request = $this->createRequest('unknown');

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf('WP_Error', $response);
    }

    #[TestDox('handleRequest() increments likes and returns success response')]
    public function testHandleRequestIncrementsLikeCounter(): void
    {
        $options = [
            ChatStatsEndpoint::OPTION_LIKED => 1,
        ];

        $wpService = new FakeWpService([
            'registerRestRoute' => true,
            'restEnsureResponse' => fn(array $response) => $this->createWpRestResponse($response),
            '__' => fn(string $string) => $string,
            'getOption' => static fn(string $optionName, int $default = 0): int => (int) ($options[$optionName] ?? $default),
            'updateOption' => static function (string $optionName, int $value) use (&$options): bool {
                $options[$optionName] = $value;
                return true;
            },
        ]);

        $endpoint = new ChatStatsEndpoint($wpService);
        $request = $this->createRequest('like');

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf('WP_REST_Response', $response);
        $this->assertSame(2, $options[ChatStatsEndpoint::OPTION_LIKED]);
    }

    #[TestDox('handleRequest() never decrements counters below zero')]
    public function testHandleRequestDoesNotGoBelowZero(): void
    {
        $options = [
            ChatStatsEndpoint::OPTION_DISLIKED => 0,
        ];

        $wpService = new FakeWpService([
            'registerRestRoute' => true,
            'restEnsureResponse' => fn(array $response) => $this->createWpRestResponse($response),
            '__' => fn(string $string) => $string,
            'getOption' => static fn(string $optionName, int $default = 0): int => (int) ($options[$optionName] ?? $default),
            'updateOption' => static function (string $optionName, int $value) use (&$options): bool {
                $options[$optionName] = $value;
                return true;
            },
        ]);

        $endpoint = new ChatStatsEndpoint($wpService);
        $request = $this->createRequest('undislike');

        $response = $endpoint->handleRequest($request);

        $this->assertInstanceOf('WP_REST_Response', $response);
        $this->assertSame(0, $options[ChatStatsEndpoint::OPTION_DISLIKED]);
    }

    private function createRequest(string $type): object
    {
        $request = $this->createMock('WP_REST_Request');
        $request->method('get_param')
            ->with('type')
            ->willReturn($type);

        return $request;
    }

    private function createWpRestResponse(array $response): object
    {
        $className = 'WP_REST_Response';
        return new $className($response);
    }
}
