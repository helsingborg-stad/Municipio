<?php

declare(strict_types=1);

namespace Municipio\Chat\Render;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Config\ChatConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ChatBubbleTest extends TestCase
{
    #[TestDox('guest sees no bubble when the only page assistant requires login')]
    public function testGuestDoesNotSeeRestrictedPageAssistant(): void
    {
        $assistant = ['name' => 'Private', 'logged_in_only' => 1, 'chat_assistant_pages' => [123]];

        static::assertSame('', $this->renderBubble($assistant, false, false));
        static::assertSame('bubble', $this->renderBubble($assistant, true, false));
    }

    #[TestDox('guest sees no bubble when the global default requires login')]
    public function testGuestDoesNotSeeRestrictedGlobalDefault(): void
    {
        $assistant = ['name' => 'Private', 'logged_in_only' => 1];

        static::assertSame('', $this->renderBubble($assistant, false, true));
        static::assertSame('bubble', $this->renderBubble($assistant, true, true));
    }

    private function renderBubble(array $assistant, bool $isUserLoggedIn, bool $globalEnabled): string
    {
        $wpService = new FakeWpService([
            'getOption' => static fn(string $name) => $name === 'options_chat_global_enabled' && $globalEnabled,
            'getQueriedObjectId' => 123,
            'getPostAncestors' => [],
            'isUserLoggedIn' => $isUserLoggedIn,
        ]);
        $acfService = new FakeAcfService([
            'getField' => static fn(string $name) => match ($name) {
                'chat_assistants' => [$assistant],
                'chat_default_assistant' => $assistant['name'],
                default => null,
            },
        ]);
        $config = new ChatConfig($wpService, $acfService);
        $renderer = new class implements ChatRenderInterface {
            public function render(ChatRenderConfigInterface $renderConfig): string
            {
                return 'bubble';
            }
        };
        $bubble = new ChatBubble($wpService, $config, $renderer);

        ob_start();
        try {
            $bubble->render();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}