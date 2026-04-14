<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Kirki\GoogleFonts;
use Kirki\Module\Webfonts\Fonts as KirkiFonts;

/**
 * Provides font choices for typography controls.
 */
class FontChoices
{
    private const GOOGLE_FONT_LIMIT = 200;

    /**
     * Returns typography field choices.
     *
     * @return array<string, array<string, array<string, string>|array<int, string>>>
     */
    public static function getTypographyChoices(): array
    {
        return [
            'fonts' => [
                'standard' => self::getStandardFontChoices(),
                'google'   => self::getEnabledGoogleFonts(),
            ],
        ];
    }

    /**
     * Returns Google font choices for the management GUI.
     *
     * @return array<string, string>
     */
    public static function getGoogleFontToggleChoices(): array
    {
        $googleFonts = (new GoogleFonts())->get_google_fonts_by_args([
            'sort'  => 'popularity',
            'count' => self::GOOGLE_FONT_LIMIT,
        ]);
        $googleFonts = array_values(array_unique(array_merge(
            $googleFonts,
            self::getEnabledGoogleFonts(),
        )));

        return array_combine(array_values($googleFonts), array_values($googleFonts));
    }

    /**
     * Returns enabled Google fonts.
     *
     * @return array<int, string>
     */
    public static function getEnabledGoogleFonts(): array
    {
        $enabledFonts = Kirki::get_option(FontCatalog::GOOGLE_FONTS_SETTING, []);
        $enabledFonts = is_array($enabledFonts) ? $enabledFonts : [];

        foreach (FontSettings::getSelectedFontFamilies() as $fontFamily) {
            if (!KirkiFonts::is_google_font($fontFamily)) {
                continue;
            }

            $enabledFonts[] = $fontFamily;
        }

        $enabledFonts = array_values(array_unique(array_filter(array_map('strval', $enabledFonts))));

        return $enabledFonts !== [] ? $enabledFonts : ['Roboto'];
    }

    /**
     * Returns standard and uploaded font choices.
     *
     * @return array<string, string>
     */
    public static function getStandardFontChoices(): array
    {
        $choices = [];

        foreach (KirkiFonts::get_standard_fonts() as $font) {
            $choices[$font['stack']] = $font['label'];
        }

        foreach (FontSettings::getUploadedFontNames() as $fontName) {
            $choices[$fontName] = $fontName;
        }

        foreach (FontSettings::getSelectedFontFamilies() as $fontFamily) {
            if (KirkiFonts::is_google_font($fontFamily) || array_key_exists($fontFamily, $choices)) {
                continue;
            }

            $choices[$fontFamily] = $fontFamily;
        }

        return $choices;
    }
}
