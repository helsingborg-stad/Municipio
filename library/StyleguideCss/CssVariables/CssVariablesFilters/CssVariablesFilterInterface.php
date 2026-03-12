<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

interface CssVariablesFilterInterface
{
    /**
     * Apply a filter to a CSS variable, allowing modification of its name or value.
     *
     * @param CssVariableInterface $cssVariable The CSS variable to filter
     * @return CssVariableInterface The filtered CSS variable
     */
    public function apply(CssVariableInterface $cssVariable): CssVariableInterface;
}
