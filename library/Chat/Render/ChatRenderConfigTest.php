<?php

declare(strict_types=1);

namespace Municipio\Chat\Render;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ChatRenderConfigTest extends TestCase
{
    #[TestDox('getAttributeList() maps lowercased assistant names to their integration type in field order')]
    public function testGetAttributeListMapsAssistantNamesToIntegrationTypes(): void
    {
        $renderConfig = $this->createRenderConfig([
            ['name' => 'Noah', 'acf_fc_layout' => 'puzzel'],
            ['name' => 'Ava', 'acf_fc_layout' => 'eneo'],
        ]);

        $attributeList = $renderConfig->getAttributeList();

        static::assertSame('{"noah":"puzzel","ava":"eneo"}', $attributeList['data-js-chat-assistants']);
        static::assertSame('1', $attributeList['data-js-municipio-chat']);
    }

    #[TestDox('getAttributeList() excludes assistants without a name or integration type')]
    public function testGetAttributeListExcludesInvalidAssistants(): void
    {
        $renderConfig = $this->createRenderConfig([
            ['name' => 'Valid Assistant', 'acf_fc_layout' => 'eneo'],
            ['name' => '', 'acf_fc_layout' => 'puzzel'],
            ['name' => 'Missing Type'],
            ['acf_fc_layout' => 'puzzel'],
            'not-an-array',
        ]);

        $attributeList = $renderConfig->getAttributeList();

        static::assertSame('{"valid assistant":"eneo"}', $attributeList['data-js-chat-assistants']);
    }

    #[TestDox('getAttributeList() returns an empty JSON list when there are no assistants')]
    public function testGetAttributeListReturnsEmptyObjectWithoutAssistants(): void
    {
        $attributeList = $this->createRenderConfig([])->getAttributeList();

        static::assertSame('[]', $attributeList['data-js-chat-assistants']);
    }

    #[TestDox('getChatId() is prefixed and stable for the same instance but unique across instances')]
    public function testGetChatIdIsStablePerInstanceAndUniqueAcrossInstances(): void
    {
        $first = $this->createRenderConfig([]);
        $second = $this->createRenderConfig([]);

        static::assertStringStartsWith('chat-', $first->getChatId());
        static::assertSame($first->getChatId(), $first->getChatId());
        static::assertNotSame($first->getChatId(), $second->getChatId());
    }

    #[TestDox('getters return the constructor values')]
    public function testGettersReturnConstructorValues(): void
    {
        $assistants = [['name' => 'Ava', 'acf_fc_layout' => 'eneo']];
        $renderConfig = $this->createRenderConfig($assistants, 'class="wp-block"');

        static::assertSame('fab', $renderConfig->getView());
        static::assertSame($assistants, $renderConfig->getAssistants());
        static::assertSame('class="wp-block"', $renderConfig->getWrapperAttributes());
    }

    #[TestDox('getLang() returns translated labels for the chat UI')]
    public function testGetLangReturnsTranslatedLabels(): void
    {
        $lang = $this->createRenderConfig([])->getLang();

        static::assertSame('Chat', $lang['chat']);
        static::assertSame('Write your question here', $lang['placeholder']);
        static::assertSame('Minimize', $lang['minimize']);
        static::assertSame(
            ['chat', 'close', 'closeChat', 'cancel', 'send', 'placeholder', 'newConversation', 'like', 'dislike', 'minimize'],
            array_keys($lang),
        );
    }

    private function createRenderConfig(array $assistants, ?string $wrapperAttributes = ''): ChatRenderConfig
    {
        $wpService = new FakeWpService([
            '__' => static fn(string $string): string => $string,
        ]);

        return new ChatRenderConfig($wpService, 'fab', $assistants, $wrapperAttributes);
    }
}
