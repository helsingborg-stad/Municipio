<?php

namespace Intranet\Admin;

class NetworkSettings
{
    public function __construct()
    {
        add_action('wpmueditblogaction', array($this, 'addCustomSiteSettings'), 999);
        add_action('wpmu_update_blog_options', array($this, 'saveCustomSiteSettings'));

        add_filter('MunicipioIntranet/site_option_fields', array($this, 'addForceSubscriptionField'), 10, 2);
        add_filter('MunicipioIntranet/site_option_fields', array($this, 'addSiteShortNameField'), 10, 2);
    }

    /**
     * Add wrapper for intranet custom settings fields
     * @param integer $blogId Blog ids
     */
    public function addCustomSiteSettings($blogId)
    {
        echo '
        <tr class="form-field">
            <th colspan="3" class="intranet-settings-wrapper">
                <strong class="intranet-settings-title">' . __('Intranet settings', 'municipio-intranet') . '</strong>
                ' . apply_filters('MunicipioIntranet/site_option_fields', '', $blogId) . '
            </td>
        </tr>
        ';
    }

    public function addSiteShortNameField($fields, $blogId)
    {
        $value = get_blog_option($blogId, 'intranet_short_name');

        $fields .= '
            <p>
                <label for="intranet-short-name">' . __('Shortname for this site', 'municipio-intranet') . '</label>
                <input type="text" class="widefat" name="intranet-short-name" id="intranet-short-name" value="' . $value . '">
            </p>
        ';

        return $fields;
    }

    /**
     * Add forece subscription checkbox to site settings
     * @param integer $blogId The current blog id
     */
    public function addForceSubscriptionField($fields, $blogId)
    {
        $checked = get_blog_option($blogId, 'intranet_force_subscription');
        $checked = checked('true', $checked, false);

        $fields .= '<p>
            <span class="checkbox">
                <input type="checkbox" name="intranet-force-subscription" id="intranet-force-subscription" value="true" ' . $checked . '>
                <label for="intranet-force-subscription">' . __('Forced subscription', 'municipio-intranet') .'</label>
            </span>
        </p>';

        return $fields;
    }

    /**
     * Save the force subscription checkbox
     * @param  integer $blogId The current blog id
     * @return boolean         Always true
     */
    public function saveCustomSiteSettings($blogId)
    {
        // Force subscription field
        if (isset($_POST['intranet-force-subscription']) && $_POST['intranet-force-subscription'] == 'true') {
            update_blog_option($blogId, 'intranet_force_subscription', 'true');
        } else {
            update_blog_option($blogId, 'intranet_force_subscription', 'false');
        }

        // Intranet short name
        if (isset($_POST['intranet-short-name']) && !empty($_POST['intranet-short-name'])) {
            update_blog_option($blogId, 'intranet_short_name', $_POST['intranet-short-name']);
        } else {
            delete_blog_option($blogId, 'intranet_short_name');
        }

        return true;
    }
}
