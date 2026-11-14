<?php

declare(strict_types=1);

namespace Municipio\Chat\Render;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AssistantKeyTest extends TestCase
{
    #[TestDox('fromName() turns "$name" into "$expected"')]
    #[DataProvider('nameProvider')]
    public function testFromName(string $name, string $expected): void
    {
        static::assertSame($expected, AssistantKey::fromName($name));
    }

    public static function nameProvider(): array
    {
        return [
            'simple name keeps lowercased form' => ['Eneo', 'eneo'],
            'spaces become dashes'              => ['Valid Assistant', 'valid-assistant'],
            'swedish characters are kept'       => ['Åsa Öberg', 'åsa-öberg'],
            'leading and trailing junk trimmed' => ['  --Bot!  ', 'bot'],
            'attribute injection stripped'      => ['x" onfocus="alert(1)', 'x-onfocus-alert-1'],
            'tag injection stripped'            => ['a><script>b', 'a-script-b'],
        ];
    }

    #[TestDox('fromName() falls back to a stable hashed key when nothing safe is left')]
    public function testFromNameFallsBackToHash(): void
    {
        $key = AssistantKey::fromName('"><');

        static::assertMatchesRegularExpression('/^assistant-[a-f0-9]{8}$/', $key);
        static::assertSame($key, AssistantKey::fromName('"><'));
    }

    #[TestDox('attributePrefix() only contains characters that are safe in an attribute name')]
    public function testAttributePrefixIsSafe(): void
    {
        $prefix = AssistantKey::attributePrefix('x" onfocus="alert(1)');

        static::assertSame('data-js-chat-x-onfocus-alert-1-', $prefix);
        static::assertDoesNotMatchRegularExpression('/[\s"\'>\/=]/', $prefix);
    }
}
