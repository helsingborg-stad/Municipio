<?php

namespace Municipio\Chat\Config;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ChatConfigTest extends TestCase
{
    #[TestDox('isEnabled() returns true when the chat_enabled option is true')]
    public function testIsEnabledReturnsTrueWhenAcfFieldIsTrue(): void
    {
        $config = new ChatConfig($this->getAcfService(['chat_enabled' => true]));

        $this->assertTrue($config->isEnabled());
    }

    #[TestDox('isEnabled() returns false when the chat_enabled option is false')]
    public function testIsEnabledReturnsFalseWhenAcfFieldIsFalse(): void
    {
        $config = new ChatConfig($this->getAcfService(['chat_enabled' => false]));

        $this->assertFalse($config->isEnabled());
    }

    #[TestDox('isEnabled() returns false when the chat_enabled option is unset')]
    public function testIsEnabledReturnsFalseWhenAcfFieldIsUnset(): void
    {
        $config = new ChatConfig($this->getAcfService());

        $this->assertFalse($config->isEnabled());
    }

    #[TestDox('isGlobalChatEnabled() returns true when the chat_global_enabled option is true')]
    public function testIsGlobalChatEnabledReturnsTrueWhenAcfFieldIsTrue(): void
    {
        $config = new ChatConfig($this->getAcfService(['chat_global_enabled' => true]));

        $this->assertTrue($config->isGlobalChatEnabled());
    }

    #[TestDox('isGlobalChatEnabled() returns false when the chat_global_enabled option is false')]
    public function testIsGlobalChatEnabledReturnsFalseWhenAcfFieldIsFalse(): void
    {
        $config = new ChatConfig($this->getAcfService(['chat_global_enabled' => false]));

        $this->assertFalse($config->isGlobalChatEnabled());
    }

    private function getAcfService(array $fields = []): FakeAcfService
    {
        return new FakeAcfService([
            'getField' => fn(string $selector) => $fields[$selector] ?? null,
        ]);
    }
}
