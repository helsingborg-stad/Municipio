<?php

namespace Municipio\Chat\Config;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\DetermineLocale;
use WpService\Implementations\FakeWpService;

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

        $this->assertInstanceOf(ChatConfig::class, new ChatConfig($this->getWpService(), $acfService));
    }

    #[TestDox('isEnabled() returns true when chat_enabled field is truthy')]
    #[RunInSeparateProcess]
    public function testIsEnabledReturnsTrueWhenChatEnabledFieldIsTruthy(): void
    {
        $acfService = $this->getAcfService(['chat_enabled' => 1]);

        $config = new ChatConfig($this->getWpService(), $acfService);

        $this->assertTrue($config->isEnabled());
    }

    #[TestDox('isGlobalChatEnabled() returns false when chat_global_enabled field is falsy')]
    public function testIsGlobalChatEnabledReturnsFalseWhenFieldIsFalsy(): void
    {
        $acfService = $this->getAcfService(['chat_global_enabled' => 0]);

        $config = new ChatConfig($this->getWpService(), $acfService);

        $this->assertFalse($config->isGlobalChatEnabled());
    }

    #[TestDox('getAssistants() returns assistants when field is an array')]
    public function testGetAssistantsReturnsAssistantsWhenFieldIsArray(): void
    {
        $assistants = [
            ['name' => 'Ava', 'greetings_phrase' => 'Hello'],
            ['name' => 'Noah', 'greetings_phrase' => 'Hi'],
        ];

        $acfService = $this->getAcfService(['chat_assistants' => $assistants]);

        $config = new ChatConfig($this->getWpService(), $acfService);

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

        $config = new ChatConfig($this->getWpService(), $acfService);

        $this->assertSame($assistants[1], $config->getDefaultAssistant());
    }

    #[TestDox('isPresidioEnabled() returns true when the chat_presidio_enabled option is true')]
    public function testIsPresidioEnabledReturnsTrueWhenAcfFieldIsTrue(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio_enabled' => true]));

        $this->assertTrue($config->isPresidioEnabled());
    }

    #[TestDox('isPresidioEnabled() returns false when the chat_presidio_enabled option is false')]
    public function testIsPresidioEnabledReturnsFalseWhenAcfFieldIsFalse(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio_enabled' => false]));

        $this->assertFalse($config->isPresidioEnabled());
    }

    #[TestDox('isPresidioEnabled() returns false when the chat_presidio_enabled option is unset')]
    public function testIsPresidioEnabledReturnsFalseWhenAcfFieldIsUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService());

        $this->assertFalse($config->isPresidioEnabled());
    }

    #[TestDox('getPresidio() returns the configured group array')]
    public function testGetPresidioReturnsConfiguredArray(): void
    {
        $group = ['analyzer_host' => 'https://a', 'supported_languages' => [['language' => 'sv']]];
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio' => $group]));

        $this->assertSame($group, $config->getPresidio());
    }

    #[TestDox('getPresidio() returns an empty array when the field is unset')]
    public function testGetPresidioReturnsEmptyArrayWhenUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService());

        $this->assertSame([], $config->getPresidio());
    }

    #[TestDox('getPresidio() returns an empty array when the field is not an array')]
    public function testGetPresidioReturnsEmptyArrayWhenNotArray(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio' => 'not-an-array']));

        $this->assertSame([], $config->getPresidio());
    }

    #[TestDox('getPresidioAnalyzerHost() returns the configured analyzer host')]
    public function testGetPresidioAnalyzerHostReturnsConfiguredValue(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['analyzer_host' => 'https://analyzer.example.com'],
        ]));

        $this->assertSame('https://analyzer.example.com', $config->getPresidioAnalyzerHost());
    }

    #[TestDox('getPresidioAnalyzerHost() returns null when the analyzer_host sub-field is unset')]
    public function testGetPresidioAnalyzerHostReturnsNullWhenUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio' => []]));

        $this->assertNull($config->getPresidioAnalyzerHost());
    }

    #[TestDox('getPresidioAnalyzerHost() returns null when the chat_presidio group is unset')]
    public function testGetPresidioAnalyzerHostReturnsNullWhenGroupUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService());

        $this->assertNull($config->getPresidioAnalyzerHost());
    }

    #[TestDox('getPresidioAnalyzerHost() returns null when the analyzer_host value is not a string')]
    public function testGetPresidioAnalyzerHostReturnsNullWhenNotString(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['analyzer_host' => 123],
        ]));

        $this->assertNull($config->getPresidioAnalyzerHost());
    }

    #[TestDox('getPresidioAnonymizerHost() returns the configured anonymizer host')]
    public function testGetPresidioAnonymizerHostReturnsConfiguredValue(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_host' => 'https://anonymizer.example.com'],
        ]));

        $this->assertSame('https://anonymizer.example.com', $config->getPresidioAnonymizerHost());
    }

    #[TestDox('getPresidioAnonymizerHost() returns null when the anonymizer_host sub-field is unset')]
    public function testGetPresidioAnonymizerHostReturnsNullWhenUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio' => []]));

        $this->assertNull($config->getPresidioAnonymizerHost());
    }

    #[TestDox('getPresidioAnonymizerHost() returns null when the anonymizer_host value is not a string')]
    public function testGetPresidioAnonymizerHostReturnsNullWhenNotString(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_host' => ['not' => 'a string']],
        ]));

        $this->assertNull($config->getPresidioAnonymizerHost());
    }

    #[TestDox('getPresidioLanguage() returns the supported language matching the page locale')]
    public function testGetPresidioLanguageReturnsLanguageMatchingPageLocale(): void
    {
        $config = new ChatConfig(
            $this->getWpService('sv_SE'),
            $this->getAcfService([
                'chat_presidio' => [
                    'supported_languages' => [
                        ['language' => 'en'],
                        ['language' => 'sv'],
                    ],
                ],
            ]),
        );

        $this->assertSame('sv', $config->getPresidioLanguage());
    }

    #[TestDox('getPresidioLanguage() falls back to the first supported language when the page locale is unsupported')]
    public function testGetPresidioLanguageFallsBackToFirstSupportedLanguageWhenLocaleUnsupported(): void
    {
        $config = new ChatConfig(
            $this->getWpService('de_DE'),
            $this->getAcfService([
                'chat_presidio' => [
                    'supported_languages' => [
                        ['language' => 'en'],
                        ['language' => 'sv'],
                    ],
                ],
            ]),
        );

        $this->assertSame('en', $config->getPresidioLanguage());
    }

    #[TestDox('getPresidioLanguage() matches case-insensitively against the page locale')]
    public function testGetPresidioLanguageMatchesCaseInsensitively(): void
    {
        $config = new ChatConfig(
            $this->getWpService('SV_SE'),
            $this->getAcfService([
                'chat_presidio' => [
                    'supported_languages' => [
                        ['language' => 'EN'],
                        ['language' => 'Sv'],
                    ],
                ],
            ]),
        );

        $this->assertSame('Sv', $config->getPresidioLanguage());
    }

    #[TestDox('getPresidioLanguage() returns null when supported_languages is unset')]
    public function testGetPresidioLanguageReturnsNullWhenSupportedLanguagesUnset(): void
    {
        $config = new ChatConfig($this->getWpService('sv_SE'), $this->getAcfService(['chat_presidio' => []]));

        $this->assertNull($config->getPresidioLanguage());
    }

    #[TestDox('getPresidioLanguage() returns null when supported_languages is empty and locale does not match')]
    public function testGetPresidioLanguageReturnsNullWhenSupportedLanguagesEmpty(): void
    {
        $config = new ChatConfig(
            $this->getWpService('sv_SE'),
            $this->getAcfService(['chat_presidio' => ['supported_languages' => []]]),
        );

        $this->assertNull($config->getPresidioLanguage());
    }

    #[TestDox('getPresidioLanguage() returns null when the chat_presidio group is unset')]
    public function testGetPresidioLanguageReturnsNullWhenGroupUnset(): void
    {
        $config = new ChatConfig($this->getWpService('sv_SE'), $this->getAcfService());

        $this->assertNull($config->getPresidioLanguage());
    }

    #[TestDox('getPresidioAnonymizerConfig() returns the decoded array when valid JSON is configured')]
    public function testGetPresidioAnonymizerConfigReturnsDecodedArray(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_config' => '{"DEFAULT":{"type":"replace","new_value":"<X>"}}'],
        ]));

        $this->assertSame(
            ['DEFAULT' => ['type' => 'replace', 'new_value' => '<X>']],
            $config->getPresidioAnonymizerConfig(),
        );
    }

    #[TestDox('getPresidioAnonymizerConfig() returns an empty array when the JSON is "{}"')]
    public function testGetPresidioAnonymizerConfigReturnsEmptyArrayForEmptyJsonObject(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_config' => '{}'],
        ]));

        $this->assertSame([], $config->getPresidioAnonymizerConfig());
    }

    #[TestDox('getPresidioAnonymizerConfig() returns null when the JSON is invalid')]
    public function testGetPresidioAnonymizerConfigReturnsNullWhenJsonIsInvalid(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_config' => 'not-json'],
        ]));

        $this->assertNull($config->getPresidioAnonymizerConfig());
    }

    #[TestDox('getPresidioAnonymizerConfig() returns null when the JSON decodes to a non-array (scalar)')]
    public function testGetPresidioAnonymizerConfigReturnsNullWhenJsonIsScalar(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_config' => '"a string"'],
        ]));

        $this->assertNull($config->getPresidioAnonymizerConfig());
    }

    #[TestDox('getPresidioAnonymizerConfig() returns null when the field is unset')]
    public function testGetPresidioAnonymizerConfigReturnsNullWhenUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio' => []]));

        $this->assertNull($config->getPresidioAnonymizerConfig());
    }

    #[TestDox('getPresidioAnonymizerConfig() returns null when the value is not a string')]
    public function testGetPresidioAnonymizerConfigReturnsNullWhenValueIsNotString(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['anonymizer_config' => ['already' => 'array']],
        ]));

        $this->assertNull($config->getPresidioAnonymizerConfig());
    }

    #[TestDox('getPresidioAllowList() extracts the word values from the configured repeater rows')]
    public function testGetPresidioAllowListReturnsExtractedWords(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => [
                'allow_list' => [
                    ['word' => 'Helsingborg'],
                    ['word' => 'Stockholm'],
                ],
            ],
        ]));

        $this->assertSame(['Helsingborg', 'Stockholm'], $config->getPresidioAllowList());
    }

    #[TestDox('getPresidioAllowList() returns an empty array when the allow_list field is unset')]
    public function testGetPresidioAllowListReturnsEmptyArrayWhenUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService(['chat_presidio' => []]));

        $this->assertSame([], $config->getPresidioAllowList());
    }

    #[TestDox('getPresidioAllowList() returns an empty array when the chat_presidio group is unset')]
    public function testGetPresidioAllowListReturnsEmptyArrayWhenGroupUnset(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService());

        $this->assertSame([], $config->getPresidioAllowList());
    }

    #[TestDox('getPresidioAllowList() returns an empty array when allow_list is not an array')]
    public function testGetPresidioAllowListReturnsEmptyArrayWhenNotArray(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => ['allow_list' => 'not-an-array'],
        ]));

        $this->assertSame([], $config->getPresidioAllowList());
    }

    #[TestDox('getPresidioAllowList() returns an empty array when a row is missing its word sub-field')]
    public function testGetPresidioAllowListReturnsEmptyArrayWhenRowMissingWord(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => [
                'allow_list' => [
                    ['word' => 'Helsingborg'],
                    ['not_word' => 'oops'],
                ],
            ],
        ]));

        $this->assertSame([], $config->getPresidioAllowList());
    }

    #[TestDox('getPresidioAllowList() returns an empty array when a row word is not a string')]
    public function testGetPresidioAllowListReturnsEmptyArrayWhenWordIsNotString(): void
    {
        $config = new ChatConfig($this->getWpService(), $this->getAcfService([
            'chat_presidio' => [
                'allow_list' => [
                    ['word' => 'Helsingborg'],
                    ['word' => 123],
                ],
            ],
        ]));

        $this->assertSame([], $config->getPresidioAllowList());
    }

    private function getAcfService(array $fields = []): FakeAcfService
    {
        return new FakeAcfService([
            'getField' => fn(string $selector) => $fields[$selector] ?? null,
        ]);
    }

    private function getWpService(string $locale = 'en_US'): FakeWpService
    {
        return new FakeWpService(['determineLocale' => $locale]);
    }
}
