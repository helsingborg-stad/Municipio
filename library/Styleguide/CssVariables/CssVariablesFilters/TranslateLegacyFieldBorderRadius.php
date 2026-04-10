<?php

namespace Municipio\Styleguide\CssVariables\CssVariablesFilters;

use Municipio\Styleguide\CssVariables\CssVariable;
use Municipio\Styleguide\CssVariables\CssVariableInterface;

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
