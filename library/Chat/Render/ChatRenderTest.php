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
        $bladeRenderer = new class () implements RendererInterface {
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
        $bladeRenderer = new class () implements RendererInterface {
            public function render(string $view, array $data = []): string
            {
                return '<section>rendered chat</section>';
            }
        };
        $render = new ChatRender($bladeRenderer);
        $config = new class () implements ChatRenderConfigInterface {
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
    }
}
