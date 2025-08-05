<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V25 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        MigrateThemeMod::migrate('hamburger_menu_appearance_type', 'mega_menu_appearance_type');
        MigrateThemeMod::migrate('hamburger_menu_custom_colors', 'mega_menu_custom_colors');
        MigrateThemeMod::migrate('hamburger_menu_font', 'mega_menu_font');
        MigrateThemeMod::migrate('hamburger_menu_item_style', 'mega_menu_item_style');
        MigrateThemeMod::migrate('hamburger_menu_item_button_style', 'mega_menu_item_button_style');
        MigrateThemeMod::migrate('hamburger_menu_item_button_color', 'mega_menu_item_button_color');
        MigrateThemeMod::migrate('hamburger_menu_color_scheme', 'mega_menu_color_scheme');
        MigrateThemeMod::migrate('hamburger_menu_mobile', 'mega_menu_mobile');

        $menuLocations = get_theme_mod('nav_menu_locations');
        if (!empty($menuLocations) && isset($menuLocations['hamburger-menu'])) {
            $menuLocations['mega-menu'] = $menuLocations['hamburger-menu'];
            unset($menuLocations['hamburger-menu']);
            set_theme_mod('nav_menu_locations', $menuLocations);
        }

    }
}