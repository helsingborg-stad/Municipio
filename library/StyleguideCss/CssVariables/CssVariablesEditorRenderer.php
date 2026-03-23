<?php

namespace Municipio\StyleguideCss\CssVariables;

class CssVariablesEditorRenderer implements CssVariablesRendererInterface
{
    public function render(CssVariableInterface ...$cssVariables): string
    {
        $css = implode("\n", array_map(fn($var) => (string) $var, $cssVariables));
        return ":root :where(.editor-styles-wrapper) {\n$css\n}";
    }
}
