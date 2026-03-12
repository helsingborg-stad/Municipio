<?php

namespace Municipio\StyleguideCss\CssVariables;

interface CssVariablesRendererInterface
{
    public function render(CssVariableInterface ...$cssVariables): string;
}
