<?php

namespace Intranet\Admin;

class NetworkSettings
{
    public function __construct()
    {
        add_action('wpmueditblogaction', array($this, 'addForceSubscriptionField'), 999);
        add_action('wpmu_update_blog_options', array($this, 'saveForceSubscriptionField'));
    }

    /**
     * Add forece subscription checkbox to site settings
     * @param integer $blogId The current blog id
     */
    public function addForceSubscriptionField($blogId)
    {
        $checked = get_blog_option($blogId, 'intranet_force_subscription');
        $checked = checked('true', $checked, false);

        echo '<tr class="form-field">
            <th scope="row"><label for="intranet-force-follow">' . __('Forced subscription', 'municipio-intranet') .'</label></th>
            <td><input type="checkbox" name="intranet-force-subscription" id="intranet-force-subscription" value="true" ' . $checked . '></td>
        </tr>';
    }

    /**
     * Save the force subscription checkbox
     * @param  integer $blogId The current blog id
     * @return boolean         Always true
     */
    public function saveForceSubscriptionField($blogId)
    {
        $res = false;

        if (isset($_POST['intranet-force-subscription']) && $_POST['intranet-force-subscription'] == 'true') {
            $res = update_blog_option($blogId, 'intranet_force_subscription', 'true');
        } else {
            $res = update_blog_option($blogId, 'intranet_force_subscription', 'false');
        }

        return true;
    }
}
