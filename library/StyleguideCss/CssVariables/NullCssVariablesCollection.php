<?php

namespace Municipio\StyleguideCss\CssVariables;

/**
 * A null implementation of the CssVariablesCollectionInterface that returns an empty array.
 * This can be used as a default or placeholder when no CSS variables are needed, e.g. in tests or when the feature is disabled.
 */
class NullCssVariablesCollection implements CssVariablesCollectionInterface
{
    public function getVariables(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return '';
    }
}
