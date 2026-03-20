<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

class TranslateLegacyFieldBorderRadius implements CssVariablesFilterInterface
{
    public function apply(CssVariableInterface $cssVariable): CssVariableInterface
    {
        if ($cssVariable->getName() !== '--c-field--border-radius' || !is_numeric($cssVariable->getValue())) {
            return $cssVariable;
        }

        return new CssVariable($cssVariable->getName(), (string) (float) $cssVariable->getValue() / 4);
    }
}
