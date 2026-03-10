<?php

namespace Municipio\StyleguideCss\CssVariables;

interface CssVariableInterface
{
    /**
     * Get the name of the CSS variable (e.g., --primary-color)
     */
    public function getName(): string;

    /**
     * Get the value of the CSS variable (e.g., #ff0000)
     */
    public function getValue(): string;
}
