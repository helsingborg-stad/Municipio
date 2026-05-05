<?php

declare(strict_types=1);

namespace Kirki\Compatibility;

if (!class_exists(Kirki::class, false)) {
    /**
     * Test stub for Kirki option retrieval.
     */
    class Kirki
    {
        /**
         * @var array<int, array{0: string, 1: string}>
         */
        public static array $calls = [];

        /**
         * @var array<string, mixed>
         */
        public static array $values = [];

        public static function reset(): void
        {
            self::$calls = [];
            self::$values = [];
        }

        /**
         * @param string $configId
         * @param string $fieldId
         *
         * @return mixed
         */
        public static function get_option($configId = '', $fieldId = '')
        {
            self::$calls[] = [(string) $configId, (string) $fieldId];

            return self::$values[$fieldId] ?? null;
        }
    }
}

namespace Municipio\Customizer\Fonts;

use Kirki\Compatibility\Kirki as KirkiCompatibility;
use Municipio\Customizer;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests managed font settings reads.
 */
class FontSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        KirkiCompatibility::reset();
    }

    #[TestDox('getUploadedFontNames() fetches uploaded fonts through configured Kirki option')]
    public function testGetUploadedFontNamesFetchesUploadedFontsThroughConfiguredKirkiOption(): void
    {
        KirkiCompatibility::$values = [
            FontCatalog::UPLOADED_FONTS_SETTING => [
                ['file' => 'https://example.com/inter.woff2'],
                ['file' => 'https://example.com/open_sans.woff'],
                ['file' => 'https://example.com/inter.woff2'],
                ['file' => 10],
                ['name' => 'Legacy Font Name'],
                'invalid-row',
                ['name' => ''],
            ],
        ];

        $fontNames = FontSettings::getUploadedFontNames();

        static::assertSame(['Inter', 'Open Sans', 'Legacy Font Name'], $fontNames);
        static::assertSame(
            [[Customizer::KIRKI_CONFIG, FontCatalog::UPLOADED_FONTS_SETTING]],
            KirkiCompatibility::$calls,
        );
    }

    #[TestDox('getSelectedFontFamilies() reads typography options through configured Kirki option')]
    public function testGetSelectedFontFamiliesReadsTypographyOptionsThroughConfiguredKirkiOption(): void
    {
        KirkiCompatibility::$values = [
            'typography_base' => ['font-family' => 'Roboto'],
            'typography_heading' => ['font-family' => 'Open Sans'],
            'typography_bold' => ['font-family' => 'Roboto'],
            'typography_italic' => ['font-family' => ''],
            'typography_lead' => ['variant' => '500'],
            'header_brand_font_settings' => 'invalid',
        ];

        $fontFamilies = FontSettings::getSelectedFontFamilies();

        static::assertSame(['Roboto', 'Open Sans'], $fontFamilies);
        static::assertSame(
            [
                [Customizer::KIRKI_CONFIG, 'typography_base'],
                [Customizer::KIRKI_CONFIG, 'typography_heading'],
                [Customizer::KIRKI_CONFIG, 'typography_bold'],
                [Customizer::KIRKI_CONFIG, 'typography_italic'],
                [Customizer::KIRKI_CONFIG, 'typography_lead'],
                [Customizer::KIRKI_CONFIG, 'header_brand_font_settings'],
            ],
            KirkiCompatibility::$calls,
        );
    }

    #[TestDox('getSelectedFontFamiliesFromThemeMods() reads typography font families from theme mods')]
    public function testGetSelectedFontFamiliesFromThemeModsReadsTypographyFontFamiliesFromThemeMods(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => match ($key) {
                'typography_base' => ['font-family' => 'Arimo', 'variant' => 'regular'],
                'typography_heading' => ['font-family' => 'Open Sans', 'variant' => '600'],
                'typography_bold' => ['font-family' => 'Arimo', 'variant' => '700'],
                'typography_italic' => ['variant' => 'italic'],
                'typography_lead' => [],
                'header_brand_font_settings' => 'invalid',
                default => $default,
            },
        ]);

        $fontFamilies = FontSettings::getSelectedFontFamiliesFromThemeMods($wpService);

        static::assertSame(['Arimo', 'Open Sans'], $fontFamilies);
    }

    #[TestDox('getSelectedFontVariantsFromThemeMods() groups selected theme mod variants by font family')]
    public function testGetSelectedFontVariantsFromThemeModsGroupsSelectedThemeModVariantsByFontFamily(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => match ($key) {
                'typography_base' => ['font-family' => 'Arimo'],
                'typography_heading' => ['font-family' => 'Arimo', 'variant' => '600'],
                'typography_bold' => ['font-family' => 'Arimo'],
                'typography_italic' => ['font-family' => 'Arimo'],
                'typography_lead' => ['font-family' => 'Open Sans'],
                'header_brand_font_settings' => ['font-family' => 'Open Sans', 'variant' => '300italic'],
                default => $default,
            },
        ]);

        $fontVariants = FontSettings::getSelectedFontVariantsFromThemeMods($wpService);

        static::assertSame(
            [
                'Arimo' => ['regular', '600', '700', 'italic'],
                'Open Sans' => ['500', '300italic'],
            ],
            $fontVariants,
        );
    }
}
