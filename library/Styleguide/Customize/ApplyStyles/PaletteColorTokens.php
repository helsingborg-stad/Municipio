<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\Styleguide\Customize\TokenData\ColorTokenDefinitions;

/** Projects content colour schemes onto palette controls without styling the WordPress admin. */
class PaletteColorTokens
{
    public function getCss(array $storedTokens, array $schemeQueries): string
    {
        $selector = '.municipio-palette-color-selector';
        $defaults = array_column(ColorTokenDefinitions::getSettings(), 'default', 'variable');
        $base = array_replace($defaults, array_intersect_key($storedTokens['token'], $defaults));
        $converter = new DesignTokensToCssConverter\DesignTokensToCssConverter();
        $css = $converter->convertRootTokens($base, $selector);

        foreach ($schemeQueries as $scheme => $query) {
            $overrides = ColorSchemeOverrides::filter($storedTokens[$scheme]);
            if ($overrides !== []) {
                $css .= sprintf('@media %s {%s}', $query, $converter->convertRootTokens($overrides, $selector));
            }
        }

        return $css;
    }
}
