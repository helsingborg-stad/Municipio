<?php

namespace Municipio\StyleguideCss\CssVariables;

interface CssVariablesRendererInterface
{
    public function render(CssVariablesCollectionInterface $cssVariablesCollection): string;
}
