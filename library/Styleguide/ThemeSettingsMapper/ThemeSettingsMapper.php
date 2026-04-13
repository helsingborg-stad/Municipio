<?php

namespace Municipio\Styleguide\ThemeSettingsMapper;

use Municipio\Styleguide\CssVariables\CssVariable;

class ThemeSettingsMapper implements ThemeSettingsMapperInterface
{
    private const THEME_SETTING_TO_CSS_VARIABLE_MAP = [
        'color_background.background' => '--color--background',
        'color_text.base' => ['--color--surface-contrast', '--color--background-contrast'],
        'color_palette_primary.base' => '--color--primary',
        'color_palette_primary.contrasting' => '--color--primary-contrast',
        'color_palette_secondary.base' => '--color--secondary',
        'color_palette_secondary.contrasting' => '--color--secondary-contrast',
        'color_card.background' => '--color--surface',
        'color_alpha.base' => '--color--alpha',
        'color_alpha.contrasting' => '--color--alpha-contrast',
        'footer_subfooter_colors.background' => '--c-footer--subfooter-color-background',
        'footer_subfooter_colors.text' => '--c-footer--subfooter-color-text',
        'footer_background.background-color' => '--c-footer--color--surface',
        'footer_color_text' => '--c-footer--color--surface-contrast',
        'typography_base.font-family' => '--font-family-base',
        'typography_base.font-size' => '--base-font-size',
        'typography_h1.font-family' => '--font-family-heading',
        'drop_shadow_color' => '--shadow-color',
        'drop_shadow_amount' => '--shadow-amount',
        'radius_md' => '--border-radius',
        'container' => '--container-width',
        'footer_logotype_height' => '--c-footer--logotype-height',
        'color_button_primary.base' => '--c-button--color--primary',
        'color_button_primary.contrasting' => '--c-button--color--primary-contrast',
        'header_logotype_height' => '--c-header--logotype-height',
        'header_brand-color' => '--c-header--brand-color',
        'border_width_outline' => '--border-width',
        'field_border_radius' => '--c-field--border-radius',
        'color_input.border' => '--c-field--color--surface-border',
    ];

    public function map(array $themeSettings): array
    {
        // echo '<pre>' . print_r($themeSettings, true) . '</pre>';
        // die();
        $cssVariables = [];

        foreach (self::THEME_SETTING_TO_CSS_VARIABLE_MAP as $themeSettingKey => $cssVariableName) {
            $themeSettingValue = $this->getNestedValue($themeSettings, explode('.', $themeSettingKey));
            if ($themeSettingValue !== null) {
                if (is_array($cssVariableName)) {
                    foreach ($cssVariableName as $name) {
                        $cssVariables[] = new CssVariable($name, $themeSettingValue);
                    }
                } else {
                    $cssVariables[] = new CssVariable($cssVariableName, $themeSettingValue);
                }
            }
        }

        return $cssVariables;
    }

    private function getNestedValue(array $array, array $keys)
    {
        $value = $array;
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }
        return $value;
    }
}
