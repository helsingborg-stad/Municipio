<?php

namespace Municipio\Admin\Roles;

use Municipio\Helper\User\User;

/**
 * Editor
 */
class Editor
{
    /**
     * Constructor
     */
    public function __construct(
        private User $userHelper,
    ) {
        if ($userHelper->userHasRole('editor')) {
            add_action('admin_init', array($this, 'adminRedirects'), 1);
            add_action('admin_menu', array($this, 'adminMenus'), 9000);
        }

        //Force unfiltered HTML. This feature is handled by laravel blade.
        add_filter('acf/allow_unfiltered_html', '__return_true');
    }

    /**
     * Redirects and limitations for editors
     */
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

    /**
     * Limitations for editors
     */
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
        $this->removeFromAdminMenuAdvanced('admin.php?page=wp_stream_settings');
        $this->removeFromAdminMenuAdvanced('admin.php?page=wp_stream');

        //Remove gravityforms admin pages
        $this->removeFromAdminMenu('admin.php?page=gf_settings');
        $this->removeFromAdminMenu('admin.php?page=gf_export');
        $this->removeFromAdminMenu('admin.php?page=gf_update');
        $this->removeFromAdminMenu('admin.php?page=gf_addons');
        $this->removeFromAdminMenu('admin.php?page=gf_help');
    }

    /**
     * Add capabilities
     */
    public function addCapabilities()
    {
        $administrator = get_role('administrator');
        $editor = get_role('editor');

        // Site admins
        $administrator->add_cap('unfiltered_html');
        $administrator->add_cap('read_private_anys');

        // Editors
        $editor->add_cap('edit_theme_options');
        $editor->add_cap('manage_options');
        $editor->add_cap('gform_full_access');
        $editor->add_cap('unfiltered_html');
        $editor->add_cap('read_private_anys');
    }

    /**
     * Redirect to dashboard
     */
    public function redirectToDashboard()
    {
        if ($this->userHelper->userHasRole('editor')) {
            wp_redirect(admin_url());
            exit();
        }
    }

    /**
     * Remove from admin menu
     */
    public function removeFromAdminMenu($slug, $subMenuSlug = null)
    {
        if (is_null($subMenuSlug)) {
            remove_menu_page($slug);
        } else {
            remove_submenu_page($slug, $subMenuSlug);
        }
    }

    /**
     * Remove from admin menu advanced
     */
    public function removeFromAdminMenuAdvanced($slug)
    {
        global $submenu;

        $slug = str_replace('admin.php?page=', '', $slug);

        if (!isset($submenu[$slug])) {
            return;
        }

        unset($submenu[$slug]);
    }
}
