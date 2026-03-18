<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

class TranslateLegacyHeaderLogotypeHeight implements CssVariablesFilterInterface
{
    public function apply(CssVariableInterface $cssVariable): CssVariableInterface
    {
        if ($cssVariable->getName() !== '--c-header--logotype-height' || !is_numeric($cssVariable->getValue())) {
            return $cssVariable;
        }

        $value = (float) $cssVariable->getValue();

        return new CssVariable($cssVariable->getName(), "calc({$value} * var(--base))");
    }
}
