<?php

namespace Municipio\Upgrade\V41;

class MigrateCustomCss
{
    private const LAYER_NAME = 'theme';
    private const REPLACEMENT_MAP = [
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

    public function migrate(string $css, array $replacementMap = self::REPLACEMENT_MAP): string
    {
        $css = str_replace(array_keys($replacementMap), array_values($replacementMap), $css);

        $css = $this->maybeWrapInCssLayer($css);

        return $css;
    }

    private function maybeWrapInCssLayer(string $css): string
    {
        if (str_contains($css, '@layer')) {
            return $css;
        }

        return '@layer ' . self::LAYER_NAME . " {\n" . $css . "\n}";
    }
}
