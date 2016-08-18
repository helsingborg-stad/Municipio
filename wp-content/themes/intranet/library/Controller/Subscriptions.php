<?php

namespace Intranet\Controller;

class Subscriptions extends \Intranet\Controller\BaseController
{
    public function init()
    {
        global $wp_query;
        global $authordata;

        // Save form if posted
        $this->saveForm();

        // Get other data
        $user = get_user_by('slug', $wp_query->query['subscriptions']);

        if (!$user) {
            $user = wp_get_current_user();
        }

        $authordata = $user;

        $this->data['user'] = $user;

        // Sites excluding forced subscriptions
        $this->data['sites'] = array_filter(\Intranet\Helper\Multisite::getSitesList(true), function ($item) {
            return !$item->is_forced;
        });
    }

    /**
     * Saves the user settings form
     * @return boolean
     */
    private function saveForm()
    {
        global $wp_query;

        if (!isset($_POST['_wpnonce'])) {
            return;
        }

        $user = get_user_by('slug', $wp_query->query['subscriptions']);
        if (!$user) {
            $user = wp_get_current_user();
        }

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'user_subscriptions_update_' . $user->ID)) {
            return;
        }

        if (!isset($_POST['user_subscriptions']) || !is_array($_POST['user_subscriptions'])) {
            return;
        }

        \Intranet\User\Subscription::update($user->ID, $_POST['user_subscriptions']);

        return true;
    }
}
