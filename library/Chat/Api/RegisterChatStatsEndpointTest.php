<?php

namespace Municipio\Chat\Api;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Municipio\Chat\Config\ChatConfigInterface;
use WpService\Implementations\FakeWpService;

class RegisterChatStatsEndpointTest extends TestCase
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
        $endpoint = new ChatStatsEndpoint($wpService);
        $config = new class () implements ChatConfigInterface {
            public function isEnabled(): bool
            {
                return false;
            }

            public function isGlobalChatEnabled(): bool
            {
                return false;
            }

            public function getDefaultAssistant(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return [];
            }
        };
        $this->assertInstanceOf(RegisterChatStatsEndpoint::class, new RegisterChatStatsEndpoint($endpoint, $config));
    }

    #[TestDox('addHooks() can be called')]
    public function testAddHooksCanBeCalled(): void
    {
        $wpService = new FakeWpService([
            'registerRestRoute' => true,
            'restEnsureResponse' => fn(array $response) => $response,
            '__' => fn(string $string) => $string,
            'getOption' => 0,
            'updateOption' => true,
        ]);
        $endpoint = new ChatStatsEndpoint($wpService);
        $config = new class () implements ChatConfigInterface {
            public function isEnabled(): bool
            {
                return false;
            }

            public function isGlobalChatEnabled(): bool
            {
                return false;
            }

            public function getDefaultAssistant(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return [];
            }
        };
        $register = new RegisterChatStatsEndpoint($endpoint, $config);
        $this->assertNull($register->addHooks());
    }
}
