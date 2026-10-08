<?php

declare(strict_types=1);

namespace Municipio\Chat\Render;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ChatRenderViewCompatibilityTest extends TestCase
{
    #[TestDox('chatBubble view contains both neutral and legacy wrapper and bubble bootstrap attributes')]
    public function testChatBubbleViewContainsNeutralAndLegacyBootstrapAttributes(): void
    {
        $template = $this->readTemplate('chatBubble.blade.php');

        static::assertStringContainsString("'data-js-chat-wrapper' => 'true'", $template);
        static::assertStringContainsString("'data-js-municipio-ai-chat-wrapper' => 'true'", $template);
        static::assertStringContainsString("'data-js-chat-bubble' => '1'", $template);
        static::assertStringContainsString("'municipio-ai-chat-bubble' => '1'", $template);
    }

    #[TestDox('block view contains both neutral and legacy block bootstrap attributes')]
    public function testBlockViewContainsNeutralAndLegacyBootstrapAttributes(): void
    {
        $template = $this->readTemplate('block.blade.php');

        static::assertStringContainsString("'data-js-chat-block' => '1'", $template);
        static::assertStringContainsString("'municipio-ai-chat-block'", $template);
    }

    private function readTemplate(string $fileName): string
    {
        $path = __DIR__ . '/views/' . $fileName;
        $content = file_get_contents($path);

        static::assertIsString($content);

        return $content;
    }
}
