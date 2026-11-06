<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ColorSchemeOverridesTest extends TestCase
{
    #[TestDox('uses the Styleguide token library to keep only theme-editable variables')]
    public function testFiltersUsingStyleguideTokenLibrary(): void
    {
        $tokens = ColorSchemeOverrides::filter([
            '--color--primary' => '#111111',
            '--color--palette-10-contrast' => '#ffffff',
            '--color--border-mix-amount' => '100%',
            '--shadow-color' => 'rgba(0, 0, 0, 0)',
            '--color--primary-border' => '#222222',
            '--color--background-contrast-muted' => '#333333',
            '--not-a-design-token' => '#444444',
        ]);

        static::assertSame([
            '--color--primary' => '#111111',
            '--color--palette-10-contrast' => '#ffffff',
            '--color--border-mix-amount' => '100%',
            '--shadow-color' => 'rgba(0, 0, 0, 0)',
        ], $tokens);
    }

    #[TestDox('rejects scheme values that can escape CSS declarations or style tags')]
    public function testRejectsUnsafeSchemeValues(): void
    {
        $tokens = ColorSchemeOverrides::filter([
            '--color--primary' => '#111111;} body { color: red',
            '--color--secondary' => '</style><script>alert(1)</script>',
            '--color--background' => 'var(--safe-color)',
            '--color--border-mix-amount' => '25%',
        ]);

        static::assertSame([
            '--color--background' => 'var(--safe-color)',
            '--color--border-mix-amount' => '25%',
        ], $tokens);
    }

    #[TestDox('keeps only safe, well-formed root custom properties')]
    public function testFiltersUnsafeRootTokenValues(): void
    {
        $tokens = ColorSchemeOverrides::filterSafeRootTokens([
            '--color--primary' => '#111111',
            '--border-radius' => 'calc(var(--base) / 2)',
            '--unsafe; color' => '#222222',
            '--color--secondary' => '#222222; } body { display: none',
        ]);

        static::assertSame([
            '--color--primary' => '#111111',
            '--border-radius' => 'calc(var(--base) / 2)',
        ], $tokens);
    }
}
