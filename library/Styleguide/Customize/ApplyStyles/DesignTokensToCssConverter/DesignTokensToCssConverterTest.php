<?php

namespace Municipio\Styleguide\Customize\ApplyStyles\DesignTokensToCssConverter;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DesignTokensToCssConverterTest extends TestCase
{
    #[TestDox('returns empty string when no design tokens are provided')]
    public function testReturnsEmptyStringWhenNoDesignTokensAreProvided()
    {
        $converter = new DesignTokensToCssConverter();
        static::assertSame('', $converter->convert([]));
    }

    #[TestDox('adds general tokens to css root')]
    public function testAddsGeneralTokensToCssRoot()
    {
        $converter = new DesignTokensToCssConverter();
        $tokens = [
            '--border-radius' => 2.75,
        ];

        $css = $converter->convert($tokens);

        $rows = explode("\n", $css);
        static::assertSame(':root {', trim($rows[0]));
        static::assertSame('--border-radius: 2.75;', trim($rows[1]));
        static::assertSame('}', trim($rows[2]));
    }

    #[TestDox('adds general component tokens to component class')]
    public function testAddsGeneralComponentTokensToComponentClass()
    {
        $converter = new DesignTokensToCssConverter();
        $tokens = [
            '__general__' => [
                'footer' => [
                    '--c-footer--color-surface' => '#000000',
                ],
            ],
        ];

        $css = $converter->convert($tokens);

        $rows = explode("\n", $css);
        static::assertSame('.c-footer {', trim($rows[0]));
        static::assertSame('--c-footer--color-surface: #000000;', trim($rows[1]));
        static::assertSame('}', trim($rows[2]));
    }

    #[TestDox('adds scoped component tokens to scoped component class')]
    public function testAddsScopedComponentTokensToScopedComponentClass()
    {
        $converter = new DesignTokensToCssConverter();
        $tokens = [
            'scope:s-header' => [
                'header' => [
                    '--c-header--space' => 0.25,
                ],
            ],
        ];

        $css = $converter->convert($tokens);

        $rows = explode("\n", $css);
        static::assertSame('[data-scope*="s-header;"] .c-header {', trim($rows[0]));
        static::assertSame('--c-header--space: 0.25;', trim($rows[1]));
        static::assertSame('}', trim($rows[2]));
    }
}
