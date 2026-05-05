<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Reads managed and legacy font settings.
 */
class FontSettings
{
    /**
     * Known typography settings containing font family selections.
     *
     * @var array<int, string>
     */
    private const FONT_SETTING_KEYS = [
        'typography_base',
        'typography_heading',
        'typography_bold',
        'typography_italic',
        'typography_lead',
        'header_brand_font_settings',
    ];

    /**
     * Default variants by typography setting when the theme mod has no explicit variant.
     *
     * @var array<string, string>
     */
    private const FONT_SETTING_DEFAULT_VARIANTS = [
        'typography_base' => 'regular',
        'typography_heading' => '700',
        'typography_bold' => '700',
        'typography_italic' => 'italic',
        'typography_lead' => '500',
        'header_brand_font_settings' => 'regular',
    ];

    /**
     * Returns uploaded font names from managed settings.
     *
     * @return array<int, string>
     */
    public static function getUploadedFontNames(): array
    {
        $uploadedFonts = \Kirki\Compatibility\Kirki::get_option(
            \Municipio\Customizer::KIRKI_CONFIG,
            FontCatalog::UPLOADED_FONTS_SETTING,
        );

        if (!is_array($uploadedFonts)) {
            return [];
        }

        $fontNames = [];

        foreach ($uploadedFonts as $uploadedFont) {
            if (!is_array($uploadedFont)) {
                continue;
            }

            if (array_key_exists('file', $uploadedFont) && is_string($uploadedFont['file']) && $uploadedFont['file'] !== '') {
                $fontName = pathinfo(basename($uploadedFont['file']), PATHINFO_FILENAME);
                $fontName = trim(str_replace(['-', '_'], ' ', $fontName));

                if ($fontName !== '') {
                    $fontNames[] = ucwords($fontName);
                    continue;
                }
            }

            if (array_key_exists('name', $uploadedFont) && is_string($uploadedFont['name']) && $uploadedFont['name'] !== '') {
                $fontNames[] = $uploadedFont['name'];
            }
        }

        return array_values(array_unique($fontNames));
    }

    /**
     * Returns font families currently selected in typography settings.
     *
     * @return array<int, string>
     */
    public static function getSelectedFontFamilies(): array
    {
        $fontFamilies = [];

        foreach (self::FONT_SETTING_KEYS as $settingKey) {
            $value = \Kirki\Compatibility\Kirki::get_option(
                \Municipio\Customizer::KIRKI_CONFIG,
                $settingKey,
            );

            if (!is_array($value) || !array_key_exists('font-family', $value) || $value['font-family'] === '') {
                continue;
            }

            $fontFamilies[] = (string) $value['font-family'];
        }

        return array_values(array_unique($fontFamilies));
    }

    /**
     * Returns font families currently selected in typography theme mods.
     *
     * @param WpService $wpService
     *
     * @return array<int, string>
     */
    public static function getSelectedFontFamiliesFromThemeMods(WpService $wpService): array
    {
        return array_keys(self::getSelectedFontVariantsFromThemeMods($wpService));
    }

    /**
     * Returns selected font families with the variants used by typography theme mods.
     *
     * @param WpService $wpService
     *
     * @return array<string, array<int, string>>
     */
    public static function getSelectedFontVariantsFromThemeMods(WpService $wpService): array
    {
        $fonts = [];

        foreach (self::FONT_SETTING_KEYS as $settingKey) {
            $value = $wpService->getThemeMod($settingKey, []);

            if (!is_array($value) || !array_key_exists('font-family', $value) || $value['font-family'] === '') {
                continue;
            }

            $fontFamily = (string) $value['font-family'];
            $variant = self::resolveVariantForSetting($settingKey, $value);

            $fonts[$fontFamily] ??= [];

            if (!in_array($variant, $fonts[$fontFamily], true)) {
                $fonts[$fontFamily][] = $variant;
            }
        }

        return $fonts;
    }

    /**
     * Resolves a font variant from a typography theme mod value.
     *
     * @param string $settingKey
     * @param array<string, mixed> $value
     *
     * @return string
     */
    private static function resolveVariantForSetting(string $settingKey, array $value): string
    {
        if (isset($value['variant']) && is_string($value['variant']) && $value['variant'] !== '') {
            return $value['variant'];
        }

        return self::FONT_SETTING_DEFAULT_VARIANTS[$settingKey] ?? 'regular';
    }
}
