<?php

namespace Municipio\StyleguideCss\ThemeSettingsMapper;

use Municipio\StyleguideCss\CssVariables\CssVariablesCollectionInterface;

interface ThemeSettingsMapperInterface
{
    /**
     * Maps theme settings to an css variable collection
     *
     * @param array $themeSettings The theme settings to map
     * @return CssVariablesCollectionInterface The mapped css variable collection
     */
    public function map(array $themeSettings): CssVariablesCollectionInterface;
}
