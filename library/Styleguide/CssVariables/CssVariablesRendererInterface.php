<?php

namespace Municipio\Styleguide\CssVariables;

interface CssVariablesRendererInterface
{
    public function render(CssVariableInterface ...$cssVariables): string;
}
