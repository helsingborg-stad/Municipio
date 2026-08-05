<?php

declare(strict_types=1);

namespace Municipio\Theme;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FooterColumnsResolverTest extends TestCase
{
    #[TestDox('resolves footer columns from a stored token json string')]
    public function testResolvesColumnsFromTokenJsonString(): void
    {
        $tokens = json_encode([
            'token' => [],
            'component' => [
                '__general__' => [
                    'footer' => [
                        '--c-footer--columns-count' => 4,
                    ],
                ],
            ],
        ]);

        $resolver = new FooterColumnsResolver();

        $result = $resolver->resolveFromThemeMods($tokens, 2, 'columns');

        static::assertSame(4, $result);
    }

    #[TestDox('resolves footer columns from decoded token arrays')]
    public function testResolvesColumnsFromDecodedTokenArray(): void
    {
        $tokens = [
            'component' => [
                '__general__' => [
                    'footer' => [
                        '--c-footer--columns-count' => '3',
                    ],
                ],
            ],
        ];

        $resolver = new FooterColumnsResolver();

        $result = $resolver->resolveFromThemeMods($tokens, 6, 'columns');

        static::assertSame(3, $result);
    }

    #[TestDox('falls back to legacy footer columns when tokens are missing')]
    public function testFallsBackToLegacyColumnsWhenTokensAreMissing(): void
    {
        $resolver = new FooterColumnsResolver();

        $result = $resolver->resolveFromThemeMods('', 5, 'columns');

        static::assertSame(5, $result);
    }

    #[TestDox('legacy basic style forces a single footer column when tokens are missing')]
    public function testLegacyBasicStyleForcesSingleColumnWhenTokensAreMissing(): void
    {
        $resolver = new FooterColumnsResolver();

        $result = $resolver->resolveFromThemeMods('', 5, 'basic');

        static::assertSame(1, $result);
    }

    #[TestDox('returns one footer column when no valid token or legacy values exist')]
    public function testReturnsOneColumnWhenNoValidValuesExist(): void
    {
        $resolver = new FooterColumnsResolver();

        $result = $resolver->resolveFromThemeMods('{"invalid":true}', null, null);

        static::assertSame(1, $result);
    }
}
