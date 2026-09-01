<?php

declare(strict_types=1);

namespace Municipio\Helper {
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

    if (!function_exists(__NAMESPACE__ . '\\get_theme_mod')) {
        function get_theme_mod(string $name, mixed $default = false): mixed
        {
            return ColorSwatchesTestState::$themeMods[$name] ?? $default;
        }
    }

    if (!function_exists(__NAMESPACE__ . '\\set_theme_mod')) {
        function set_theme_mod(string $name, mixed $value): bool
        {
            ColorSwatchesTestState::$themeMods[$name] = $value;
            ColorSwatchesTestState::$setThemeModCalls[] = [$name, $value];
            return true;
        }
    }

    if (!function_exists(__NAMESPACE__ . '\\wp_json_encode')) {
        function wp_json_encode(mixed $value): string|false
        {
            return json_encode($value);
        }
    }
}

namespace Municipio\Upgrade\V47 {
    use Municipio\Helper\ColorSwatches;
    use Municipio\Helper\ColorSwatchesTestState;
    use PHPUnit\Framework\Attributes\TestDox;
    use PHPUnit\Framework\TestCase;

    class Version47Test extends TestCase
    {
        protected function setUp(): void
        {
            ColorSwatches::$cachedColors = null;
            ColorSwatchesTestState::$themeMods = [];
            ColorSwatchesTestState::$setThemeModCalls = [];
        }

        #[TestDox('upgradeToVersion migrates legacy palette colors to token palette slots')]
        public function testUpgradeToVersionMigratesLegacyPaletteColorsToTokenPaletteSlots(): void
        {
            ColorSwatchesTestState::$themeMods = [
                'tokens' => '{}',
                'color_palette_primary' => ['base' => '#123456'],
                'color_palette_secondary' => ['base' => '#abcdef'],
                'color_palette_additional' => ['#ff0000'],
            ];

            (new Version47())->upgradeToVersion();

            static::assertCount(1, ColorSwatchesTestState::$setThemeModCalls);

            $savedTokens = json_decode((string) ColorSwatchesTestState::$setThemeModCalls[0][1], true, 512, JSON_THROW_ON_ERROR);

            static::assertSame('#123456', $savedTokens['token']['--color--palette-1']);
            static::assertSame('#abcdef', $savedTokens['token']['--color--palette-2']);
            static::assertSame('#ff0000', $savedTokens['token']['--color--palette-3']);
        }
    }
}
