<?php

declare(strict_types=1);

namespace Municipio\Chat\Render;

use Municipio\Chat\Config\ChatConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ChatRenderConfigTest extends TestCase
{
    #[TestDox('getChatId() returns an assistant specific slug id')]
    public function testGetChatIdReturnsAssistantSpecificSlugId(): void
    {
        $assistantName = 'City Guide Assistant';
        $renderConfig = $this->createRenderConfig([
            ['name' => $assistantName],
        ], $assistantName);

        static::assertSame(
            sprintf('global-chat-city-guide-assistant-%s', substr(md5($assistantName), 0, 8)),
            $renderConfig->getChatId(),
        );
    }

    #[TestDox('getChatId() returns a deterministic hash id when assistant name cannot be slugged')]
    public function testGetChatIdReturnsDeterministicHashIdWhenAssistantNameCannotBeSlugged(): void
    {
        $renderConfig = $this->createRenderConfig([
            ['name' => '---'],
        ], '---');

        static::assertMatchesRegularExpression('/^global-chat-[a-f0-9]{8}$/', $renderConfig->getChatId());
    }

    #[TestDox('getChatId() falls back to default when assistant cannot be resolved')]
    public function testGetChatIdFallsBackToDefaultWhenAssistantCannotBeResolved(): void
    {
        $renderConfig = $this->createRenderConfig([
            ['name' => 'Known Assistant'],
        ], 'Unknown Assistant');

        static::assertSame('global-chat-default', $renderConfig->getChatId());
    }

    private function createRenderConfig(array $assistants, string $assistantName): ChatRenderConfig
    {
        $wpService = new FakeWpService([
            '__' => static fn(string $string): string => $string,
        ]);

        $chatConfig = new class($assistants) implements ChatConfigInterface {
            public function __construct(
                private array $assistants,
            ) {}

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
                return $this->assistants[0] ?? null;
            }

            public function getAssistantForActiveQuery(): ?array
            {
                return $this->getDefaultAssistant();
            }

            public function getAssistants(): array
            {
                return $this->assistants;
            }

            public function isPresidioEnabled(): bool
            {
                return false;
            }

            public function getPresidioAnalyzerHost(): ?string
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

            public function getPresidioAnonymizerConfig(): ?array
            {
                return null;
            }

            public function getPresidioAllowList(): array
            {
                return [];
            }
        };

        return new ChatRenderConfig($wpService, $chatConfig, 'fab', $assistantName);
    }
}
