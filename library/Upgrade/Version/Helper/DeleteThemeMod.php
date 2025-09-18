<?php

namespace Municipio\Upgrade\Version\Helper;

class DeleteThemeMod
{
    /**
     * Deletes a theme mod
     *
     * @param string $key
     * @return bool
     */
    public static function delete(string $key)
    {
        return remove_theme_mod($key);
    }
}