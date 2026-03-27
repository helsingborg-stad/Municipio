<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

class TranslateLegacyBorderRadius implements CssVariablesFilterInterface
{
    public function apply(CssVariableInterface $cssVariable): CssVariableInterface
    {
        if ($cssVariable->getName() !== '--border-radius' || !is_numeric($cssVariable->getValue())) {
            return $cssVariable;
        }

        return new CssVariable($cssVariable->getName(), (string) (float) $cssVariable->getValue() / 8);
    }
}
