<?php

namespace Municipio\Styleguide\CssVariables\CssVariablesFilters;

use Municipio\Styleguide\CssVariables\CssVariableInterface;

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
