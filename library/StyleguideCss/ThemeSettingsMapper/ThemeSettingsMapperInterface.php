<?php

namespace Municipio\StyleguideCss\ThemeSettingsMapper;

use Municipio\StyleguideCss\CssVariables\CssVariableInterface;

interface ThemeSettingsMapperInterface
{
    /**
     * Maps theme settings to an array of CSS variables
     *
     * @param array $themeSettings The theme settings to map
     * @return CssVariableInterface[] An array of CSS variables
     */
    public function map(array $themeSettings): array;
}
