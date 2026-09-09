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
        $bladeRenderer = new class() implements RendererInterface {
            public function render(string $view, array $data = []): string
            {
                return '<div>chat</div>';
            }
        };
        $this->assertInstanceOf(ChatRender::class, new ChatRender($bladeRenderer));
    }

    #[TestDox('render() can be called')]
    public function testRenderCanBeCalled(): void
    {
        $renderData = null;

        $bladeRenderer = new class($renderData) implements RendererInterface {
            private mixed $renderData;

            public function __construct(&$renderData)
            {
                $this->renderData = &$renderData;
            }

            public function render(string $view, array $data = []): string
            {
                $this->renderData = $data;
                return '<section>rendered chat</section>';
            }
        };
        $render = new ChatRender($bladeRenderer);
        $config = new class() implements ChatRenderConfigInterface {
            public function getView(): string
            {
                return 'block';
            }

            public function getAssistant(): ?array
            {
                return ['name' => 'Ava'];
            }

            public function getWrapperAttributes(): ?string
            {
                return '';
            }

            public function getAssistantName(): ?string
            {
                return 'Ava';
            }

            public function getChatId(): string
            {
                return 'global-chat-ava';
            }

            public function getAvatar(): ?array
            {
                return [];
            }

            public function getGreetingsPhrase(): ?string
            {
                return null;
            }

            public function getAttributeList(): array
            {
                return [];
            }

            public function getLang(): array
            {
                return [];
            }
        };

        $this->assertIsString($render->render($config));
        $this->assertIsArray($renderData);
        $this->assertSame('global-chat-ava', $renderData['chatId']);
    }
}
