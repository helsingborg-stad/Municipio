<?php

namespace Municipio\Upgrade\V41;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Class Version41
 */
class Version41 implements VersionInterface
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
        'footer_logotype_height' => 'token.--c-footer--logotype-height',
        'color_button_primary.base' => 'token.--c-button--color--primary',
        'color_button_primary.contrasting' => 'token.--c-button--color--primary-contrast',
        'header_logotype_height' => 'component.__general__.header.--c-header--logotype-height',
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
    ];

    /**
     * Constructor.
     */
    public function __construct(
        private WpGetCustomCss&WpUpdateCustomCssPost&GetOption&SetThemeMod $wpService,
        private GetField&UpdateField $acfService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $this->migrateCustomizerSettingsToDesignTokens();

        $this->migrateCustomCss();

        // $this->migrateThemeMods();
    }

    private function migrateCustomizerSettingsToDesignTokens(): void
    {
        $themeMods = $this->wpService->getOption('theme_mods_municipio', []);
        // echo '<pre>' . print_r($themeMods, true) . '</pre>';
        // die();
        $tokens = $this->getMigratedTokens($themeMods);

        $this->wpService->setThemeMod('tokens', json_encode($tokens));
    }

    public function getMigratedTokens(array $themeMods): array
    {
        $tokens = $this->map($themeMods);
        return $this->decorate($tokens);
    }

    /**
     * Apply custom transformations to certain targets.
     *
     * @param array $cssVariables The array of CSS variables to transform.
     * @return array The transformed array of CSS variables.
     */
    private function decorate(array $tokens): array
    {
        if (!is_null($tokens['token']['--container-width'] ?? null)) {
            $tokens['token']['--container-width'] .= 'px';
        }

        if (!is_null($tokens['token']['--border-radius'] ?? null)) {
            $tokens['token']['--border-radius'] = (float) $tokens['token']['--border-radius'] / 8;
        }

        if (!is_null($tokens['token']['--border-width'] ?? null)) {
            $tokens['token']['--border-width'] = (float) $tokens['token']['--border-width'] / 8;
        }

        if (!is_null($tokens['component']['__general__']['field']['--c-field--border-radius'] ?? null)) {
            $tokens['component']['__general__']['field']['--c-field--border-radius'] = (string) (float) $tokens['component']['__general__']['field']['--c-field--border-radius'] / 4;
        }

        if (!is_null($tokens['component']['__general__']['card']['--c-card--border-width'] ?? null)) {
            $tokens['component']['__general__']['card']['--c-card--border-width'] = (string) (float) $tokens['component']['__general__']['card']['--c-card--border-width'] / 8;
        }

        if (!is_null($tokens['component']['__general__']['header']['--c-header--logotype-height'] ?? null)) {
            $tokens['component']['__general__']['header']['--c-header--logotype-height'] = ((int) $tokens['component']['__general__']['header']['--c-header--logotype-height'] * 8) . 'px';
        }

        return $tokens;
    }

    public function map(array $themeSettings): array
    {
        $mapped = [
            'token' => ['--font-size-scale-ratio' => 1.200],
            'component' => [],
        ];

        foreach (self::THEMEMODS_TOKENS_MAP as $themeSettingKey => $tokenKey) {
            $themeSettingValue = $this->getNestedValue($themeSettings, explode('.', $themeSettingKey));

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

    private function migrateThemeMods(): void
    {
        $themeMods = $this->wpService->getOption('theme_mods_municipio', []);

        if (isset($themeMods['quicklinks_appearance_type']) && $themeMods['quicklinks_appearance_type'] === 'custom') {
            $themeMods['quicklinks_color_scheme'] = 'secondary';
        }

        unset($themeMods['quicklinks_appearance_type']);
        unset($themeMods['quicklinks_background_type']);
        unset($themeMods['quicklinks_custom_background']);

        $this->wpService->updateOption('theme_mods_municipio', $themeMods);
    }

    private function maybeWrapInCssLayer(string $css): string
    {
        if (str_contains($css, '@layer')) {
            return $css;
        }

        return "@layer theme {\n" . $css . "\n}";
    }

    private function migrateCustomCss(): void
    {
        $customCss = $this->wpService->wpGetCustomCss();
        $acfCustomCss = $this->acfService->getField('custom_css_input', 'option');

        $searchReplaceMap = [
            '--color-header-background' => '--c-header--background-color',
            '--color-primary' => '--color--primary',
            '--color-secondary' => '--color--secondary',
            '--color-breadcrumb-icon' => '--c-breadcrumb--color--background-contrast-muted',
            '--color-background' => '--color--background',
            '--c-header-logotype-height' => '--c-header--logotype-height',
            '--c-header-brand-color' => '--c-header--brand-color',
            '--color--primary-light' => '--color--primary',
            'height: calc(var(--c-header--logotype-height, 6) * var(--base, 8px));' => 'height: var(--c-header--logotype-height);',
            'width: calc(var(--c-header--logotype-height, 6) * var(--base, 8px));' => 'width: var(--c-header--logotype-height);',
        ];

        $customCss = str_replace(array_keys($searchReplaceMap), array_values($searchReplaceMap), $customCss);
        $acfCustomCss = str_replace(array_keys($searchReplaceMap), array_values($searchReplaceMap), $acfCustomCss);

        $customCss = $this->maybeWrapInCssLayer($customCss);
        $acfCustomCss = $this->maybeWrapInCssLayer($acfCustomCss);

        $this->wpService->wpUpdateCustomCssPost($customCss);
        $this->acfService->updateField('custom_css_input', $acfCustomCss, 'option');
    }
}
