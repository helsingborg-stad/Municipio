<?php

namespace Municipio\StyleguideCss\CssVariables;

use Stringable;

interface CssVariablesCollectionInterface extends Stringable
{
    /**
     * Get an array of CSS variables in the collection
     *
     * @return CssVariableInterface[] An array of CSS variable objects
     */
    public function getVariables(): array;
}
