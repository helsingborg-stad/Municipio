<?php

namespace Municipio\Theme;

class Customizer
{
    /* Get the current name key, as a css friendly key.
     * @param bool $childTheme Return the current child theme if existing.
     * @return string The name of the current child theme or parent theme.
     */
    public function getThemeKey($childTheme = false) : string
    {
        if ($childTheme === true) {
            $themeObject = wp_get_theme();
        } else {
            $themeObject = wp_get_theme(get_template());
        }

        return sanitize_title($themeObject->get("Name"));
    }
}
