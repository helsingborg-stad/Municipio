<?php

namespace Municipio\Chat\Render;

use Municipio\Chat\Config\ChatConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManager;

class ChatEnqueueTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            '__' => fn(string $string) => $string,
        ]);
        $enqueue = new EnqueueManager($wpService);
        $config = new class () implements ChatConfigInterface {
            public function isEnabled(): bool
            {
                return true;
            }

            public function isGlobalChatEnabled(): bool
            {
                return true;
            }

            public function getDefaultAssistant(): ?array
            {
                return ['name' => 'Default'];
            }

            public function getAssistants(): array
            {
                return [['name' => 'Default']];
            }
        };
        $this->assertInstanceOf(ChatEnqueue::class, new ChatEnqueue($wpService, $enqueue, $config));
    }

    #[TestDox('addHooks() can be called')]
    public function testAddHooksCanBeCalled(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            '__' => fn(string $string) => $string,
        ]);
        $enqueue = new EnqueueManager($wpService);
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
        $enqueueObj = new ChatEnqueue($wpService, $enqueue, $config);
        $this->assertNull($enqueueObj->addHooks());
    }
}
