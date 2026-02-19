<?php

namespace Municipio\Upgrade\Version\Helper;

use WP_CLI;

class MigrateAcfOptionImageIdToThemeModUrl
{
    /**
     * Move and clean out the old theme mod
     *
     * @param string $oldKey
     * @param string $newKey
     * @return bool
     */
    public static function migrate(string $option, string $themeMod)
    {
       $errorMessage = "Failed to migrate ACF option \"$option\" to theme mod \"$themeMod\"";

        if (
            !function_exists('get_field') ||
            empty($value = get_field($option, 'option', false)) ||
            !set_theme_mod($themeMod, $value)
        ) {
            WP_CLI::line($errorMessage);
            return;
        }

        delete_field($option, 'option');
    }
}