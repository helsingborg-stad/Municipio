<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

class TranslateLegacyContainerWidth implements CssVariablesFilterInterface
{
    public function apply(CssVariableInterface $cssVariable): CssVariableInterface
    {
        if ($cssVariable->getName() !== '--container-width' || !is_numeric($cssVariable->getValue())) {
            return $cssVariable;
        }

        return new CssVariable($cssVariable->getName(), $cssVariable->getValue() . 'px');
    }
}
