<?php

namespace Municipio\Styleguide\CssVariables\CssVariablesFilters;

use Municipio\Styleguide\CssVariables\CssVariable;
use Municipio\Styleguide\CssVariables\CssVariableInterface;

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
