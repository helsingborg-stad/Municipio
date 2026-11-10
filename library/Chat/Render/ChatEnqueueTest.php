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
        $config = $this->createChatConfig(true, true, ['name' => 'Default'], [['name' => 'Default']]);
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
        $config = $this->createChatConfig(false, false, null, []);
        $enqueueObj = new ChatEnqueue($wpService, $enqueue, $config);

        static::assertNull($enqueueObj->addHooks());
    }

    private function createChatConfig(
        bool $isEnabled,
        bool $isGlobalChatEnabled,
        ?array $defaultAssistant,
        array $assistants,
    ): ChatConfigInterface {
        return new class($isEnabled, $isGlobalChatEnabled, $defaultAssistant, $assistants) implements ChatConfigInterface {
            public function __construct(
                private bool $isEnabled,
                private bool $isGlobalChatEnabled,
                private ?array $defaultAssistant,
                private array $assistants,
            ) {}

            public function isEnabled(): bool
            {
                return $this->isEnabled;
            }

            public function isGlobalChatEnabled(): bool
            {
                return $this->isGlobalChatEnabled;
            }

            public function getDefaultAssistant(): ?array
            {
                return $this->defaultAssistant;
            }

            public function getAssistantForActiveQuery(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return $this->assistants;
            }

            public function isAssistantAllowed(array $assistant): bool
            {
                return true;
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
    }
}
