<?php

namespace Municipio\StyleguideCss\CssVariables;

class CssVariablesRenderer implements CssVariablesRendererInterface
{
    public function render(CssVariablesCollectionInterface $cssVariablesCollection): string
    {
        return ":root {\n$cssVariablesCollection\n}";
    }
}
