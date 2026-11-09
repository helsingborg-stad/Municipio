<?php

namespace Municipio\Chat\Render;

use ComponentLibrary\Renderer\RendererInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ChatRenderTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ChatRender::class, new ChatRender($this->createBladeRenderer()));
    }

    #[TestDox('render() returns an empty string and skips blade when there are no assistants')]
    public function testRenderReturnsEmptyStringWithoutAssistants(): void
    {
        $bladeRenderer = $this->createBladeRenderer();
        $render = new ChatRender($bladeRenderer);

        $this->assertSame('', $render->render($this->createRenderConfig([])));
        $this->assertNull($bladeRenderer->view);
    }

    #[TestDox('render() wraps the blade output and passes view, chat id, lang and attributes')]
    public function testRenderWrapsBladeOutputAndPassesData(): void
    {
        $bladeRenderer = $this->createBladeRenderer();
        $render = new ChatRender($bladeRenderer);

        $output = $render->render($this->createRenderConfig([['name' => 'Ava']], 'class="wp-block"'));

        $this->assertSame('<div class="wp-block"><section>rendered chat</section></div>', $output);
        $this->assertSame('block', $bladeRenderer->view);
        $this->assertSame('chat-123', $bladeRenderer->data['chatId']);
        $this->assertSame(['send' => 'Send'], $bladeRenderer->data['lang']);
        $this->assertSame(['data-js-municipio-chat' => '1'], $bladeRenderer->data['attributeList']);
    }

    #[TestDox('render() lets each assistant integration add its attributes and ignores unknown integrations')]
    public function testRenderAppliesIntegrationTransforms(): void
    {
        $bladeRenderer = $this->createBladeRenderer();
        $render = new ChatRender($bladeRenderer);

        $render->render($this->createRenderConfig([
            ['name' => 'Ava', 'acf_fc_layout' => 'eneo', 'avatar' => 'ava.png'],
            ['name' => 'Noah', 'acf_fc_layout' => 'puzzel', 'queue_key' => 'q1'],
            ['name' => 'Ghost', 'acf_fc_layout' => 'unknown'],
            ['name' => 'NoType'],
        ]));

        $attributes = $bladeRenderer->data['attributeList'];
        $this->assertSame('1', $attributes['data-js-municipio-chat']);
        $this->assertSame('ava.png', $attributes['data-js-chat-ava-avatar']);
        $this->assertSame('q1', $attributes['data-js-chat-noah-queue-key']);
        $this->assertArrayNotHasKey('data-js-chat-ghost-name', $attributes);
        $this->assertArrayNotHasKey('data-js-chat-notype-name', $attributes);
    }

    private function createBladeRenderer(): RendererInterface
    {
        return new class() implements RendererInterface {
            public ?string $view = null;
            public array $data = [];

            public function render(string $view, array $data = []): string
            {
                $this->view = $view;
                $this->data = $data;
                return '<section>rendered chat</section>';
            }
        };
    }

    private function createRenderConfig(array $assistants, ?string $wrapperAttributes = ''): ChatRenderConfigInterface
    {
        return new class($assistants, $wrapperAttributes) implements ChatRenderConfigInterface {
            public function __construct(
                private array $assistants,
                private ?string $wrapperAttributes,
            ) {}

            public function getView(): string
            {
                return 'block';
            }

            public function getAssistants(): array
            {
                return $this->assistants;
            }

            public function getWrapperAttributes(): ?string
            {
                return $this->wrapperAttributes;
            }

            public function getChatId(): string
            {
                return 'chat-123';
            }

            public function getAttributeList(): array
            {
                return ['data-js-municipio-chat' => '1'];
            }

            public function getLang(): array
            {
                return ['send' => 'Send'];
            }
        };
    }
}
