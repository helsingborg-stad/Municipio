<?php

declare(strict_types=1);

namespace Municipio\Chat\Render\Integration;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class EneoTest extends TestCase
{
    #[TestDox('implements RenderIntegrationInterface')]
    public function testImplementsInterface(): void
    {
        static::assertInstanceOf(RenderIntegrationInterface::class, new Eneo());
    }

    #[TestDox('transform() adds name, greeting phrase and avatar attributes with a lowercased name prefix')]
    public function testTransformAddsPrefixedAttributes(): void
    {
        $result = (new Eneo())->transform([], [
            'name' => 'MyAssistant',
            'greetings_phrase' => 'Hello there',
            'avatar' => 'https://example.com/avatar.png',
        ]);

        static::assertSame([
            'attributeList' => [
                'data-js-chat-myassistant-name' => 'MyAssistant',
                'data-js-chat-myassistant-greeting-phrase' => 'Hello there',
                'data-js-chat-myassistant-avatar' => 'https://example.com/avatar.png',
            ],
        ], $result);
    }

    #[TestDox('transform() defaults greeting phrase and avatar to empty strings when missing')]
    public function testTransformDefaultsMissingKeys(): void
    {
        $result = (new Eneo())->transform([], ['name' => 'Bot']);

        static::assertSame('', $result['attributeList']['data-js-chat-bot-greeting-phrase']);
        static::assertSame('', $result['attributeList']['data-js-chat-bot-avatar']);
    }

    #[TestDox('transform() encodes every newline style in the greeting phrase as &#10;')]
    public function testTransformEncodesNewlines(): void
    {
        $result = (new Eneo())->transform([], [
            'name' => 'Bot',
            'greetings_phrase' => "One\r\nTwo\rThree\nFour",
        ]);

        static::assertSame('One&#10;Two&#10;Three&#10;Four', $result['attributeList']['data-js-chat-bot-greeting-phrase']);
    }

    #[TestDox('transform() merges into an existing attributeList and keeps other data untouched')]
    public function testTransformMergesExistingAttributeList(): void
    {
        $data = [
            'id' => 'chat',
            'attributeList' => [
                'data-existing' => 'keep',
                'data-js-chat-bot-name' => 'old',
            ],
        ];

        $result = (new Eneo())->transform($data, ['name' => 'Bot', 'avatar' => 'a.png']);

        static::assertSame('chat', $result['id']);
        static::assertSame('keep', $result['attributeList']['data-existing']);
        static::assertSame('Bot', $result['attributeList']['data-js-chat-bot-name']);
        static::assertSame('a.png', $result['attributeList']['data-js-chat-bot-avatar']);
        static::assertCount(4, $result['attributeList']);
    }
}
