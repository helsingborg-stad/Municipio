<?php

namespace Municipio\Styleguide\CssVariables;

class CssVariablesRenderer implements CssVariablesRendererInterface
{
    public function render(CssVariableInterface ...$cssVariables): string
    {
        $css = implode("\n", array_map(fn($var) => (string) $var, $cssVariables));
        return ":root {\n$css\n}";
    }
}
