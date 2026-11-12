<?php

declare(strict_types=1);

namespace Municipio\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\\get_theme_mod')) {
    /**
     * Test double for get_theme_mod.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    function get_theme_mod(string $name, mixed $default = false): mixed
    {
        return ColorSwatchesTestState::$themeMods[$name] ?? $default;
    }
}

if (!function_exists(__NAMESPACE__ . '\\set_theme_mod')) {
    /**
     * Test double for set_theme_mod.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    function set_theme_mod(string $name, mixed $value): bool
    {
        ColorSwatchesTestState::$themeMods[$name] = $value;
        ColorSwatchesTestState::$setThemeModCalls[] = [$name, $value];
        return true;
    }
}

if (!function_exists(__NAMESPACE__ . '\\wp_json_encode')) {
    /**
     * Test double for wp_json_encode.
     *
     * @param mixed $value
     *
     * @return string|false
     */
    function wp_json_encode(mixed $value): string|false
    {
        return json_encode($value);
    }
}

if (!function_exists(__NAMESPACE__ . '\\apply_filters')) {
    /**
     * Test double for apply_filters.
     *
     * @param string $hookName
     * @param mixed  $value
     *
     * @return mixed
     */
    function apply_filters(string $hookName, mixed $value): mixed
    {
        return $value;
    }
}

if (!function_exists(__NAMESPACE__ . '\\__')) {
    function __(string $text, string $domain = 'default'): string
    {
        return $text;
    }
}

if (!class_exists(__NAMESPACE__ . '\\ColorSwatchesTestState')) {
    class ColorSwatchesTestState
    {
        /**
         * @var array<string, mixed>
         */
        public static array $themeMods = [];

        /**
         * @var array<int, array<int, mixed>>
         */
        public static array $setThemeModCalls = [];
    }
}

class ColorSwatchesTest extends TestCase
{
    protected function setUp(): void
    {
        ColorSwatches::$cachedColors = null;
        ColorSwatchesTestState::$themeMods = [];
        ColorSwatchesTestState::$setThemeModCalls = [];
    }

    #[TestDox('returns ordered swatches from stored design tokens')]
    public function testReturnsOrderedSwatchesFromStoredDesignTokens(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--primary' => '#112233',
                '--color--primary-contrast' => '#ffffff',
                '--color--secondary' => '#445566',
                '--color--secondary-contrast' => '#000000',
                '--color--palette-1' => '#778899',
                '--color--palette-1-contrast' => '#ffffff',
                '--color--background' => '#fefefe',
            ],
            'component' => [],
        ], JSON_THROW_ON_ERROR);

        $colors = ColorSwatches::getColors();

        static::assertSame(
            [
                '#112233',
                '#ffffff',
                '#445566',
                '#000000',
                '#778899',
                '#fefefe',
            ],
            $colors,
        );
        static::assertCount(0, ColorSwatchesTestState::$setThemeModCalls);
    }

    #[TestDox('migrates legacy customizer colors to design tool palette tokens once')]
    public function testMigratesLegacyCustomizerColorsToDesignToolPaletteTokensOnce(): void
    {
        ColorSwatchesTestState::$themeMods = [
            'tokens' => '{}',
            'color_palette_primary' => ['base' => '#101010'],
            'color_palette_secondary' => ['base' => '#f0f0f0'],
            'color_palette_additional' => ['#ff0000', ['campaign' => '#00ff00'], '#abc', 'blue'],
        ];

        $didMigrate = ColorSwatches::migrateLegacyCustomizerPaletteToDesignTokens();
        $colors = ColorSwatches::getColors();

        static::assertTrue($didMigrate);

        static::assertSame(
            [
                '#101010',
                '#ffffff',
                '#f0f0f0',
                '#000000',
                '#ff0000',
                '#00ff00',
                '#aabbcc',
            ],
            $colors,
        );

        static::assertCount(1, ColorSwatchesTestState::$setThemeModCalls);
        static::assertSame('tokens', ColorSwatchesTestState::$setThemeModCalls[0][0]);

        $savedTokens = json_decode((string) ColorSwatchesTestState::$setThemeModCalls[0][1], true, 512, JSON_THROW_ON_ERROR);

        static::assertSame('#101010', $savedTokens['token']['--color--palette-1']);
        static::assertSame('#ffffff', $savedTokens['token']['--color--palette-1-contrast']);
        static::assertSame('#f0f0f0', $savedTokens['token']['--color--palette-2']);
        static::assertSame('#000000', $savedTokens['token']['--color--palette-2-contrast']);
        static::assertSame('#ff0000', $savedTokens['token']['--color--palette-3']);
        static::assertSame('#00ff00', $savedTokens['token']['--color--palette-4']);
        static::assertSame('#aabbcc', $savedTokens['token']['--color--palette-5']);
    }

    #[TestDox('getTokenColor returns the stored token color, or the fallback when missing or invalid')]
    public function testGetTokenColorReturnsStoredTokenOrFallback(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--danger' => '#ABC',
                '--color--danger-contrast' => 'invalid',
            ],
        ], JSON_THROW_ON_ERROR);

        static::assertSame('#aabbcc', ColorSwatches::getTokenColor('--color--danger', '#e53935'));
        static::assertSame('#ffffff', ColorSwatches::getTokenColor('--color--danger-contrast', '#ffffff'));
        static::assertSame('#000000', ColorSwatches::getTokenColor('--color--missing', '#000000'));
    }

    #[TestDox('returns normalized palette token values keyed by token name')]
    public function testReturnsNormalizedPaletteTokenValues(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--palette-1' => '#ABC',
                '--color--palette-1-contrast' => '#000000',
                '--color--palette-2' => 'invalid',
                '--color--secondary' => '#112233',
            ],
        ], JSON_THROW_ON_ERROR);

        $tokenValues = ColorSwatches::getTokenPaletteValues();

        static::assertSame('#aabbcc', $tokenValues['--color--palette-1']);
        static::assertSame('#000000', $tokenValues['--color--palette-1-contrast']);
        static::assertSame('#112233', $tokenValues['--color--secondary']);
        static::assertArrayNotHasKey('--color--palette-2', $tokenValues);
    }

    #[TestDox('returns semantic palette choices and CSS-variable colour pairs')]
    public function testReturnsSemanticPaletteChoicesAndPairs(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--palette-1' => '#abcdef',
                '--color--palette-1-contrast' => '#000000',
            ],
        ], JSON_THROW_ON_ERROR);

        static::assertSame(
            ['primary' => 'Primary', 'secondary' => 'Secondary', 'palette-1' => 'Palette 1'],
            ColorSwatches::getColorPairChoices(),
        );
        static::assertSame(
            [
                'background' => 'var(--color--palette-1)',
                'contrast' => 'var(--color--palette-1-contrast)',
            ],
            ColorSwatches::getColorPair('palette-1'),
        );
        static::assertNull(ColorSwatches::getColorPair('#abcdef'));
    }

    #[TestDox('returns resolved background and contrast previews for visual palette selectors')]
    public function testReturnsColorPairPreviews(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--primary' => '#123456',
                '--color--primary-contrast' => '#ffffff',
                '--color--palette-1' => '#abcdef',
                '--color--palette-1-contrast' => '#000000',
            ],
        ], JSON_THROW_ON_ERROR);

        static::assertSame(
            [
                ['value' => 'primary', 'label' => 'Primary', 'background' => '#123456', 'contrast' => '#ffffff'],
                ['value' => 'secondary', 'label' => 'Secondary', 'background' => '#6e6e6e', 'contrast' => '#ffffff'],
                ['value' => 'palette-1', 'label' => 'Palette 1', 'background' => '#abcdef', 'contrast' => '#000000'],
            ],
            ColorSwatches::getColorPairPreviews(),
        );
    }

    #[TestDox('maps legacy colour literals to the nearest palette pair')]
    public function testMapsLegacyColorsToClosestPalettePairs(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--primary' => '#ff0000',
                '--color--primary-contrast' => '#ffffff',
                '--color--secondary' => '#0000ff',
                '--color--secondary-contrast' => '#000000',
            ],
        ], JSON_THROW_ON_ERROR);

        static::assertSame('primary', ColorSwatches::getClosestColorPairRole('rgb(250, 5, 5)'));
        static::assertSame('secondary', ColorSwatches::getClosestColorPairRole('#0202f8'));
        static::assertSame('primary', ColorSwatches::getClosestColorPairRole('#fafafa', 'contrast'));
        static::assertSame('secondary', ColorSwatches::normalizeColorPairRole('secondary'));
    }

    #[TestDox('migration skips legacy colors already represented by primary and secondary tokens')]
    public function testMigrationSkipsLegacyColorsAlreadyRepresentedByPrimaryAndSecondaryTokens(): void
    {
        ColorSwatchesTestState::$themeMods = [
            'tokens' => json_encode([
                'token' => [
                    '--color--primary' => '#101010',
                    '--color--secondary' => '#f0f0f0',
                ],
                'component' => [],
            ], JSON_THROW_ON_ERROR),
            'color_palette_primary' => ['base' => '#101010'],
            'color_palette_secondary' => ['base' => '#f0f0f0'],
            'color_palette_additional' => ['#00ff00', '#ff00ff'],
        ];

        $didMigrate = ColorSwatches::migrateLegacyCustomizerPaletteToDesignTokens();

        static::assertTrue($didMigrate);
        static::assertCount(1, ColorSwatchesTestState::$setThemeModCalls);

        $savedTokens = json_decode((string) ColorSwatchesTestState::$setThemeModCalls[0][1], true, 512, JSON_THROW_ON_ERROR);

        static::assertSame('#00ff00', $savedTokens['token']['--color--palette-1']);
        static::assertSame('#ff00ff', $savedTokens['token']['--color--palette-2']);
        static::assertArrayNotHasKey('--color--palette-3', $savedTokens['token']);
    }

    #[TestDox('Color getPalettes resolves requested palettes from token values')]
    public function testColorGetPalettesResolvesRequestedPalettesFromTokenValues(): void
    {
        ColorSwatchesTestState::$themeMods['tokens'] = json_encode([
            'token' => [
                '--color--primary' => '#111111',
                '--color--primary-contrast' => '#ffffff',
                '--color--secondary' => '#222222',
                '--color--secondary-contrast' => '#000000',
                '--color--background' => '#f5f5f5',
                '--color--background-contrast' => '#1a1a1a',
                '--color--palette-1' => '#ff0000',
                '--color--palette-2' => '#00ff00',
            ],
            'component' => [],
        ], JSON_THROW_ON_ERROR);

        $palettes = Color::getPalettes([
            'color_palette_primary',
            'color_palette_secondary',
            'color_background',
            'color_palette_additional',
        ]);

        static::assertSame('#111111', $palettes['color_palette_primary']['base']);
        static::assertSame('#ffffff', $palettes['color_palette_primary']['contrasting']);
        static::assertSame('#222222', $palettes['color_palette_secondary']['base']);
        static::assertSame('#000000', $palettes['color_palette_secondary']['contrasting']);
        static::assertSame('#f5f5f5', $palettes['color_background']['background']);
        static::assertSame('#1a1a1a', $palettes['color_background']['contrasting']);
        static::assertSame(['#ff0000', '#00ff00'], $palettes['color_palette_additional']);
    }
}
