<?php

declare(strict_types=1);

namespace Kirki\Compatibility;

if (!class_exists(Kirki::class)) {
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
}
