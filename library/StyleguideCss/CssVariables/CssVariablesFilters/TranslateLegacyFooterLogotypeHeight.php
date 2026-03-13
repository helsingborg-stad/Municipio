<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

class TranslateLegacyFooterLogotypeHeight implements CssVariablesFilterInterface
{
    public function apply(CssVariableInterface $cssVariable): CssVariableInterface
    {
        if ($cssVariable->getName() !== '--c-footer--logotype-height' || !is_numeric($cssVariable->getValue())) {
            return $cssVariable;
        }

        return new CssVariable($cssVariable->getName(), ((string) (float) $cssVariable->getValue() * 12) . 'px');
    }
}
