<?php

namespace Municipio\Admin\Roles;

class Editor
{
    public function __construct()
    {
        if (\Municipio\Helper\User::hasRole('editor')) {
            add_action('admin_init', array($this, 'adminRedirects'), 1);
            add_action('admin_menu', array($this, 'adminMenus'), 900);
        }
    }

    public function adminRedirects()
    {

        //Add capability
        add_action('admin_init', array($this, 'addCapabilities'));

        //Redirect from this page names
        add_action('load-tools.php', array($this, 'redirectToDashboard'));
        add_action('load-themes.php', array($this, 'redirectToDashboard'));
        add_action('load-customize.php', array($this, 'redirectToDashboard'));

        add_action('load-options-media.php', array($this, 'redirectToDashboard'));
        add_action('load-options-permalink.php', array($this, 'redirectToDashboard'));
        add_action('load-options-discussion.php', array($this, 'redirectToDashboard'));
        add_action('load-options-writing.php', array($this, 'redirectToDashboard'));
    }

    public function adminMenus()
    {

        //Edit theme options limitations
        $this->removeFromAdminMenu('tools.php');
        $this->removeFromAdminMenu('themes.php', 'customize.php?return=' . urlencode($_SERVER['REQUEST_URI']));
        $this->removeFromAdminMenu('themes.php', 'themes.php');

        //Edit settings limitations
        $this->removeFromAdminMenu('options-general.php', 'options-writing.php');
        $this->removeFromAdminMenu('options-general.php', 'options-discussion.php');
        $this->removeFromAdminMenu('options-general.php', 'options-media.php');
        $this->removeFromAdminMenu('options-general.php', 'options-permalink.php');

        //Remove ACF
        $this->removeFromAdminMenu('edit.php?post_type=acf-field-group');

        //Remove All in one SEO settings
        $this->removeFromAdminMenu('admin.php?page=all-in-one-seo-pack%2Faioseop_class.php');

        //Remove Stream
        $this->removeFromAdminMenu('admin.php?page=wp_stream_settings');
        $this->removeFromAdminMenu('admin.php?page=wp_stream');

        //Remove gravityforms admin pages
        $this->removeFromAdminMenu('admin.php?page=gf_settings');
        $this->removeFromAdminMenu('admin.php?page=gf_export');
        $this->removeFromAdminMenu('admin.php?page=gf_update');
        $this->removeFromAdminMenu('admin.php?page=gf_addons');
        $this->removeFromAdminMenu('admin.php?page=gf_help');

    }

    public function addCapabilities()
    {
        $role = get_role('editor');
        $role->add_cap('edit_theme_options');
        $role->add_cap('manage_options');
        $role->add_cap('gform_full_access');
    }

    public function redirectToDashboard()
    {
        if (\Municipio\Helper\User::hasRole('editor')) {
            wp_redirect(admin_url());
            exit;
        } else {
            header('Theme auth-system: Yay! Admins are permitted to do this.');
        }
    }

    public function removeFromAdminMenu($slug, $subMenuSlug = null)
    {
        if (is_null($subMenuSlug)) {
            remove_menu_page($slug);
        } else {
            remove_submenu_page($slug, $subMenuSlug);
        }
    }
}
