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

    #[TestDox('block renders nothing when the selected assistant does not exist')]
    public function testUnknownAssistantRendersNothing(): void
    {
        static::assertSame('', $this->renderBlock(['name' => 'Public'], true, 'Removed assistant'));
    }

    #[TestDox('Puzzel assistants are not offered in the block assistant dropdown')]
    public function testPuzzelIsNotOfferedInBlock(): void
    {
        $blockArgs = $this->registerBlock([
            ['name' => 'Ava', 'acf_fc_layout' => 'eneo'],
            ['name' => 'Noah', 'acf_fc_layout' => 'puzzel'],
        ], true, 'Ava');

        static::assertSame(['Default', 'Ava'], $blockArgs['attributes']['assistant']['enum']);
    }

    #[TestDox('block renders nothing for a Puzzel assistant, selected or as default')]
    public function testPuzzelAssistantRendersNothing(): void
    {
        $assistants = [
            ['name' => 'Ava', 'acf_fc_layout' => 'eneo'],
            ['name' => 'Noah', 'acf_fc_layout' => 'puzzel'],
        ];

        $blockArgs = $this->registerBlock($assistants, true, 'Noah');

        static::assertSame('', $blockArgs['render_callback'](['assistant' => 'Noah']));
        static::assertSame('', $blockArgs['render_callback'](['assistant' => 'Default']));
        static::assertSame('rendered', $blockArgs['render_callback'](['assistant' => 'Ava']));
    }

    #[TestDox('block is not registered when only Puzzel assistants exist')]
    public function testBlockIsNotRegisteredWithOnlyPuzzel(): void
    {
        $blockArgs = $this->registerBlock([['name' => 'Noah', 'acf_fc_layout' => 'puzzel']], true, 'Noah');

        static::assertSame([], $blockArgs);
    }

    private function renderBlock(array $assistant, bool $isUserLoggedIn, string $assistantName): string
    {
        $blockArgs = $this->registerBlock([$assistant], $isUserLoggedIn, $assistant['name']);

        return $blockArgs['render_callback'](['assistant' => $assistantName]);
    }

    private function registerBlock(array $assistants, bool $isUserLoggedIn, string $defaultAssistantName): array
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
                'chat_integrations' => $assistants,
                'chat_default_assistant' => $defaultAssistantName,
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

        return $blockArgs;
    }
}
