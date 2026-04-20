<?php

namespace Municipio\Upgrade\V41;

class MapThemeModsToDesignTokens
{
    private const THEMEMODS_TOKENS_MAP = [
        'color_background.background' => 'token.--color--background',
        'color_text.base' => ['token.--color--surface-contrast', 'token.--color--background-contrast'],
        'color_palette_primary.base' => 'token.--color--primary',
        'color_palette_primary.contrasting' => 'token.--color--primary-contrast',
        'color_palette_secondary.base' => 'token.--color--secondary',
        'color_palette_secondary.contrasting' => 'token.--color--secondary-contrast',
        'color_card.background' => 'token.--color--surface',
        'color_alpha.base' => 'token.--color--alpha',
        'color_alpha.contrasting' => 'token.--color--alpha-contrast',
        'footer_subfooter_colors.background' => 'component.__general__.footer.--c-footer--subfooter-color-background',
        'footer_subfooter_colors.text' => 'component.__general__.footer.--c-footer--subfooter-color-text',
        'footer_background.background-color' => 'component.__general__.footer.--c-footer--color--surface',
        'footer_color_text' => 'component.__general__.footer.--c-footer--color--surface-contrast',
        'typography_base.font-family' => 'token.--font-family-base',
        'typography_base.font-size' => 'token.--base-font-size',
        'typography_h1.font-family' => 'token.--font-family-heading',
        'drop_shadow_color' => 'token.--shadow-color',
        'drop_shadow_amount' => 'token.--shadow-amount',
        'radius_md' => 'token.--border-radius',
        'container' => 'token.--container-width',
        'footer_height_logotype' => 'component.__general__.footer.--c-footer--home-link-height-multiplier',
        'color_button_primary.base' => 'token.--c-button--color--primary',
        'color_button_primary.contrasting' => 'token.--c-button--color--primary-contrast',
        'header_logotype_height' => 'component.__general__.header.--c-header--logotype-height-multiplier',
        'header_brand-color' => 'token.--c-header--brand-color',
        'border_width_outline' => 'token.--border-width',
        'field_border_radius' => 'component.__general__.field.--c-field--border-radius',
        'field_custom_colors.background' => 'component.__general__.field.--c-field--color--surface-alt',
        'field_custom_colors.border-color' => 'component.__general__.field.--c-field--color--surface-border',
        'quicklinks_custom_colors.text-color' => 'component.scope:s-quicklinks-header.header.--c-header--color',
        'quicklinks_custom_colors.background' => 'component.scope:s-quicklinks-header.header.--c-header--background-color',
        'quicklinks_custom_colors.icon-color' => 'component.scope:s-quicklinks-header.header.--c-header--color',
        'border_width_card' => 'component.__general__.card.--c-card--border-width',
        'color_card.border' => 'component.__general__.card.--c-card--color--surface-border',
        'color_button_primary.base' => 'component.__general__.button.--c-button--color--primary',
        'color_button_primary.contrasting' => 'component.__general__.button.--c-button--color--primary-contrast',
        'header_brand_font_settings.font-size' => 'component.__general__.header.--c-brand-font-size',
        'main_content_padding' => 'token.--space',
        'organism_grid_gap' => 'token.--outer-space',
    ];

    public function map(array $themeMods, array $themeModsTokenMap = self::THEMEMODS_TOKENS_MAP): array
    {
        $mapped = [
            'token' => ['--font-size-scale-ratio' => 1.200],
            'component' => [],
        ];

        foreach ($themeModsTokenMap as $themeSettingKey => $tokenKey) {
            $themeSettingValue = $this->getNestedValue($themeMods, explode('.', $themeSettingKey));

            if ($themeSettingValue !== null) {
                if (is_array($tokenKey)) {
                    foreach ($tokenKey as $key) {
                        $this->setNestedValue($mapped, explode('.', $key), $themeSettingValue);
                    }
                } else {
                    $this->setNestedValue($mapped, explode('.', $tokenKey), $themeSettingValue);
                }
            }
        }

        return $this->removeEmptyValues($mapped);
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

    private function setNestedValue(array &$array, array $keys, mixed $value): void
    {
        $current = &$array;

        foreach ($keys as $key) {
            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }

            $current = &$current[$key];
        }

        $current = $value;
    }

    private function removeEmptyValues(array $tokens): array
    {
        $tokens['token'] = array_filter($tokens['token'], function ($value) {
            return $value !== null && $value !== '';
        });

        $tokens['component'] = array_filter($tokens['component'], function ($value) {
            return $value !== null && $value !== '';
        });

        return $tokens;
    }
}
