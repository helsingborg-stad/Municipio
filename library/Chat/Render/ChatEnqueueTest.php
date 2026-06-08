<?php

declare(strict_types=1);


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
            '__' => static fn(string $string) => $string,
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

            public function getAssistantForActiveQuery(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return [['name' => 'Default']];
            }

            public function isPresidioEnabled(): bool
            {
                return true;
            }

            
            public function getPresidioAllowList(): array
            {
                return [];
            }
            
            public function getPresidioAnalyzerHost(): ?string
            {
                return null;
            }
            
            public function getPresidioAnonymizerConfig(): ?array
            {
                return null;
            }
            
            public function getPresidioAnonymizerHost(): ?string
            {
                return null;
            }
            
            public function getPresidioLanguage(): ?string
            {
                return null;
            }
        };
        static::assertInstanceOf(ChatEnqueue::class, new ChatEnqueue($wpService, $enqueue, $config));
    }

    #[TestDox('addHooks() can be called')]
    public function testAddHooksCanBeCalled(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            '__' => static fn(string $string) => $string,
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

            public function getAssistantForActiveQuery(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return [];
            }
            
            public function isPresidioEnabled(): bool
            {
                return true;
            }

            
            public function getPresidioAllowList(): array
            {
                return [];
            }
            
            public function getPresidioAnalyzerHost(): ?string
            {
                return null;
            }
            
            public function getPresidioAnonymizerConfig(): ?array
            {
                return null;
            }
            
            public function getPresidioAnonymizerHost(): ?string
            {
                return null;
            }
            
            public function getPresidioLanguage(): ?string
            {
                return null;
            }
        };
        $enqueueObj = new ChatEnqueue($wpService, $enqueue, $config);

        static::assertNull($enqueueObj->addHooks());
    }
}
