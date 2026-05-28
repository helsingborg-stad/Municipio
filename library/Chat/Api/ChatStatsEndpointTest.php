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
            'restEnsureResponse' => fn(array $response) => $response,
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
}
