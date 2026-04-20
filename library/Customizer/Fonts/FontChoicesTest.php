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

namespace Kirki\Module\Webfonts;

if (!class_exists(Fonts::class)) {
    /**
     * Test stub for Kirki webfont helper methods.
     */
    class Fonts
    {
        /**
         * @var array<int, string>
         */
        private const GOOGLE_FONTS = ['Roboto', 'Lato', 'Open Sans'];

        public static function is_google_font(string $fontFamily): bool
        {
            return in_array($fontFamily, self::GOOGLE_FONTS, true);
        }

        /**
         * @return array<int, array{stack: string, label: string}>
         */
        public static function get_standard_fonts(): array
        {
            return [];
        }
    }
}

namespace Municipio\Customizer\Fonts;

use Kirki\Compatibility\Kirki as KirkiCompatibility;
use Municipio\Customizer;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests font choice retrieval.
 */
class FontChoicesTest extends TestCase
{
    protected function setUp(): void
    {
        KirkiCompatibility::reset();
    }

    #[TestDox('getEnabledGoogleFonts() reads setting using configured Kirki option and merges selected google fonts')]
    public function testGetEnabledGoogleFontsReadsSettingUsingConfiguredKirkiOptionAndMergesSelectedGoogleFonts(): void
    {
        KirkiCompatibility::$values = [
            FontCatalog::GOOGLE_FONTS_SETTING => ['Lato'],
            'typography_base' => ['font-family' => 'Roboto'],
            'typography_heading' => ['font-family' => 'Arial'],
            'typography_bold' => ['font-family' => ''],
            'typography_italic' => ['variant' => 'italic'],
            'typography_lead' => ['font-family' => 'Roboto'],
            'header_brand_font_settings' => [],
        ];

        $enabledFonts = FontChoices::getEnabledGoogleFonts();

        static::assertSame(['Lato', 'Roboto'], $enabledFonts);
        static::assertSame(
            [Customizer::KIRKI_CONFIG, FontCatalog::GOOGLE_FONTS_SETTING],
            KirkiCompatibility::$calls[0],
        );
    }
}
