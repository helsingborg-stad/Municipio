<?php

namespace Municipio\Admin\Roles;

class Editor
{
    public function __construct()
    {
        //Add capability
        add_action('admin_init', array($this, 'addCapabilities'));

        //Redirect from this page names
        add_action('load-tools.php', array($this, 'redirectToDashboard'));
        add_action('load-themes.php', array($this, 'redirectToDashboard'));
        add_action('load-customize.php', array($this, 'redirectToDashboard'));

        //Hide above pages
        add_action('admin_menu', function () {
            $this->removeFromAdminMenu('tools.php');
            $this->removeFromAdminMenu('themes.php', 'customize.php?return=' . urlencode($_SERVER['REQUEST_URI']));
            $this->removeFromAdminMenu('themes.php', 'themes.php');
        });
    }

    public function addCapabilities()
    {
        $role = get_role('editor');
        $role->add_cap('edit_theme_options');
    }

    public function redirectToDashboard()
    {
        if (current_user_can('editor') === true) {
            wp_redirect(admin_url());
            exit;
        }
    }

    public function removeFromAdminMenu($slug, $subMenuSlug = null)
    {
        if (is_null($subMenuSlug)) {
            remove_menu_page($slug, $subMenuSlug);
        } else {
            remove_submenu_page($slug, $subMenuSlug);
        }
    }
}
