<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

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
     * Returns uploaded font names from managed settings.
     *
     * @return array<int, string>
     */
    public static function getUploadedFontNames(): array
    {
        $uploadedFonts = \Kirki\Compatibility\Kirki::get_option(FontCatalog::UPLOADED_FONTS_SETTING, []);

        if (!is_array($uploadedFonts)) {
            return [];
        }

        $fontNames = [];

        foreach ($uploadedFonts as $uploadedFont) {
            if (!is_array($uploadedFont) || !array_key_exists('name', $uploadedFont) || $uploadedFont['name'] === '') {
                continue;
            }

            $fontNames[] = (string) $uploadedFont['name'];
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
            $value = \Kirki\Compatibility\Kirki::get_option($settingKey, []);

            if (!is_array($value) || !array_key_exists('font-family', $value) || $value['font-family'] === '') {
                continue;
            }

            $fontFamilies[] = (string) $value['font-family'];
        }

        return array_values(array_unique($fontFamilies));
    }
}
