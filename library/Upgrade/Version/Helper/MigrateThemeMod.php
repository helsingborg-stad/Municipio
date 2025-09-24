<?php

namespace Municipio\Upgrade\Version\Helper;

class MigrateThemeMod
{
    /**
     * Move and clean out the old theme mod
     *
     * @param string $oldKey
     * @param string $newKey
     * @return bool
     */
    public static function migrate($oldKey, $newKey, $subkey = null)
    {
        if ($oldValue = get_theme_mod($oldKey)) {
            if ($subkey && isset($oldValue[$subkey])) {
                return SetAssociativeThemeMod::set($newKey, $oldValue[$subkey]);
            } elseif (is_null($subkey)) {
                return SetAssociativeThemeMod::set($newKey, $oldValue);
            }
        }
        return false;
    }
}