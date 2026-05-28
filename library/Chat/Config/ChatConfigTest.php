<?php

namespace Municipio\Chat\Config;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests for ChatConfig.
 */
class ChatConfigTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $acfService = new FakeAcfService([
            'getField' => fn() => null,
        ]);

        $this->assertInstanceOf(ChatConfig::class, new ChatConfig($acfService));
    }

    #[TestDox('isEnabled() returns true when chat_enabled field is truthy')]
    public function testIsEnabledReturnsTrueWhenChatEnabledFieldIsTruthy(): void
    {
        $acfService = new FakeAcfService([
            'getField' => fn(string $field, string $scope) => $field === 'chat_enabled' && $scope === 'option' ? 1 : null,
        ]);

        $config = new ChatConfig($acfService);

        $this->assertTrue($config->isEnabled());
    }

    #[TestDox('isGlobalChatEnabled() returns false when chat_global_enabled field is falsy')]
    public function testIsGlobalChatEnabledReturnsFalseWhenFieldIsFalsy(): void
    {
        $acfService = new FakeAcfService([
            'getField' => fn(string $field, string $scope) => $field === 'chat_global_enabled' && $scope === 'option' ? 0 : null,
        ]);

        $config = new ChatConfig($acfService);

        $this->assertFalse($config->isGlobalChatEnabled());
    }

    #[TestDox('getAssistants() returns assistants when field is an array')]
    public function testGetAssistantsReturnsAssistantsWhenFieldIsArray(): void
    {
        $assistants = [
            ['name' => 'Ava', 'greetings_phrase' => 'Hello'],
            ['name' => 'Noah', 'greetings_phrase' => 'Hi'],
        ];

        $acfService = new FakeAcfService([
            'getField' => fn(string $field, string $scope) => $field === 'chat_assistants' && $scope === 'option' ? $assistants : null,
        ]);

        $config = new ChatConfig($acfService);

        $this->assertSame($assistants, $config->getAssistants());
    }

    #[TestDox('getDefaultAssistant() returns matching assistant by configured default name')]
    public function testGetDefaultAssistantReturnsMatchingAssistantByConfiguredDefaultName(): void
    {
        $assistants = [
            ['name' => 'Ava', 'greetings_phrase' => 'Hello'],
            ['name' => 'Noah', 'greetings_phrase' => 'Hi'],
        ];

        $acfService = new FakeAcfService([
            'getField' => function (string $field, string $scope) use ($assistants) {
                if ($scope !== 'option') {
                    return null;
                }

                if ($field === 'chat_default_assistant') {
                    return 'Noah';
                }

                if ($field === 'chat_assistants') {
                    return $assistants;
                }

                return null;
            },
        ]);

        $config = new ChatConfig($acfService);

        $this->assertSame($assistants[1], $config->getDefaultAssistant());
    }
}
