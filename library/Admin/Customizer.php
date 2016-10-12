<?php

namespace Municipio\Admin;

class Customizer
{
    public function __construct()
    {
        // Remove customizer actions
        remove_action('plugins_loaded', '_wp_customize_include', 10);
        remove_action('admin_enqueue_scripts', '_wp_customize_loader_settings', 11);

        add_action('admin_menu', array($this, 'removeFromAdminMenu'), 11);

        // Remove from admin bar
        add_action('wp_before_admin_bar_render', array($this, 'removeFromAdminBar'));

        // If anyone reaches the customizer page show error message
        add_action('load-customize.php', array($this, 'loadCustomizer'));
    }

    public function removeFromAdminMenu()
    {
        global $submenu;

        foreach ($submenu['themes.php'] as $key => $item) {
            if ($item[1] != 'customize') {
                continue;
            }

            unset($submenu['themes.php'][$key]);
        }
    }

    /**
     * Removes custimize from admin bar menu
     * @return void
     */
    public function removeFromAdminBar()
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('customize');
    }

    /**
     * Die if someone reaches the customizer page
     * @return void
     */
    public function loadCustomizer()
    {
        wp_die('Customizer is disabled. Please contact the system administrator for more information.');
    }
}
