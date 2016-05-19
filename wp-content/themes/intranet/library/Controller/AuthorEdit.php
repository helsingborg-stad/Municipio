<?php

namespace Intranet\Controller;

class AuthorEdit extends \Municipio\Controller\BaseController
{
    public function init()
    {
        global $wp_query;
        global $authordata;

        // Save form if posted
        $this->saveForm();

        // Get other data
        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
        }

        $this->data['currentUser'] = $currentUser;
        $this->data['user'] = $user;
    }

    private function saveForm()
    {
        global $wp_query;

        if (!isset($_POST['_wpnonce'])) {
            return;
        }

        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'user_settings_update_' . $user->ID)) {
            return;
        }

        update_user_meta($user->ID, 'user_work_title', sanitize_text_field($_POST['user_work_title']));
        update_user_meta($user->ID, 'user_phone', sanitize_text_field($_POST['user_phone']));
        update_user_meta($user->ID, 'user_administration_unit', sanitize_text_field($_POST['user_administration_unit']));
        update_user_meta($user->ID, 'user_department', sanitize_text_field($_POST['user_department']));

        return true;
    }
}
