<?php

declare(strict_types=1);

namespace Municipio\Chat;

use AcfService\Implementations\FakeAcfService;
use Municipio\Chat\Config\ChatConfig;
use Municipio\Chat\Render\ChatRenderConfigInterface;
use Municipio\Chat\Render\ChatRenderInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ChatBlockTest extends TestCase
{
    #[TestDox('guest does not render a logged-in-only block assistant')]
    public function testGuestDoesNotRenderRestrictedAssistant(): void
    {
        $assistant = ['name' => 'Private', 'logged_in_only' => 1];

        static::assertSame('', $this->renderBlock($assistant, false, 'Private'));
        static::assertSame('', $this->renderBlock($assistant, false, 'Default'));
    }

    #[TestDox('logged-in user can render a logged-in-only block assistant')]
    public function testLoggedInUserRendersRestrictedAssistant(): void
    {
        $assistant = ['name' => 'Private', 'logged_in_only' => 1];

        static::assertSame('rendered', $this->renderBlock($assistant, true, 'Private'));
        static::assertSame('rendered', $this->renderBlock($assistant, true, 'Default'));
    }

    #[TestDox('guest can render a public block assistant')]
    public function testGuestRendersPublicAssistant(): void
    {
        static::assertSame('rendered', $this->renderBlock(['name' => 'Public'], false, 'Public'));
    }

    private function renderBlock(array $assistant, bool $isUserLoggedIn, string $assistantName): string
    {
        $blockArgs = [];
        $wpService = new FakeWpService([
            'registerBlockType' => static function (string $name, array $args) use (&$blockArgs): false {
                $blockArgs = $args;
                return false;
            },
            'getBlockWrapperAttributes' => '',
            'isUserLoggedIn' => $isUserLoggedIn,
        ]);
        $acfService = new FakeAcfService([
            'getField' => static fn(string $name) => match ($name) {
                'chat_assistants' => [$assistant],
                'chat_default_assistant' => $assistant['name'],
                default => null,
            },
        ]);
        $renderer = new class implements ChatRenderInterface {
            public function render(ChatRenderConfigInterface $renderConfig): string
            {
                return 'rendered';
            }
        };
        $block = new ChatBlock($wpService, new ChatConfig($wpService, $acfService), $renderer);

        $block->registerBlock();

        return $blockArgs['render_callback'](['assistant' => $assistantName]);
    }
}
