<?php

namespace Intranet\Controller;

class AuthorEdit extends \Intranet\Controller\BaseController
{
    public function init()
    {
        global $wp_query;
        global $authordata;

        // Save form if posted
        $this->saveForm();

        // Get other data
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if ($user) {
            $authordata = $user;
        }

        $this->data['user'] = $user;
        $this->data['userResponsibilities'] = is_array(get_the_author_meta('user_responsibilities', $user->ID)) ? get_the_author_meta('user_responsibilities', $user->ID) : array();
        $this->data['userSkills'] = is_array(get_the_author_meta('user_skills', $user->ID)) ? get_the_author_meta('user_skills', $user->ID) : array();
        $this->data['administrationUnits'] = \Intranet\User\AdministrationUnits::getAdministrationUnits();
        $this->data['targetGroups'] = \Intranet\User\TargetGroups::getAvailableGroups();
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

        $currentUser = wp_get_current_user();
        $user = get_user_by('slug', $wp_query->query['author_name']);

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'user_settings_update_' . $user->ID)) {
            return;
        }

        if (isset($_GET['remove_profile_image']) && $_GET['remove_profile_image'] == 'true') {
            $profileImage = new \Intranet\User\ProfileImage();
            $profileImage->removeProfileImage($user->ID);
            return wp_redirect($_SERVER['HTTP_REFERER']);
        }

        if (isset($_POST['image_uploader_file'][0]) && !empty($_POST['image_uploader_file'][0])) {
            $profileImage = new \Intranet\User\ProfileImage();
            $profileImage->uploadProfileImage($_POST['image_uploader_file'][0], $user);
        }

        if (isset($_POST['user_work_title'])) {
            update_user_meta($user->ID, 'user_work_title', sanitize_text_field($_POST['user_work_title']));
        }

        $phone = sanitize_text_field($_POST['user_phone']);
        if (!empty($phone)) {
            $phone = \Intranet\Helper\DataCleaner::phoneNumber($phone);
        }

        update_user_meta($user->ID, 'user_phone', $phone);
        update_user_meta($user->ID, 'user_administration_unit', $_POST['user_administration_unit']);
        update_user_meta($user->ID, 'user_department', $_POST['user_department']);
        update_user_meta($user->ID, 'user_workplace', $_POST['user_workplace']);

        update_user_meta($user->ID, 'user_facebook_url', $_POST['user_facebook_url']);
        update_user_meta($user->ID, 'user_linkedin_url', $_POST['user_linkedin_url']);
        update_user_meta($user->ID, 'user_instagram_username', $_POST['user_instagram_username']);
        update_user_meta($user->ID, 'user_twitter_username', $_POST['user_twitter_username']);

        update_user_meta($user->ID, 'user_about', implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['user_about']))));
        update_user_meta($user->ID, 'user_target_groups', isset($_POST['user_target_groups']) ? array_map('sanitize_text_field', $_POST['user_target_groups']) : array());
        update_user_meta($user->ID, 'user_color_scheme', isset($_POST['color_scheme']) ? $_POST['color_scheme'] : 'purple');

        if (isset($_POST['responsibilities'])) {
            update_user_meta($user->ID, 'user_responsibilities', $_POST['responsibilities']);
        } else {
            delete_user_meta($user->ID, 'user_responsibilities');
        }

        if (isset($_POST['skills'])) {
            update_user_meta($user->ID, 'user_skills', $_POST['skills']);
        } else {
            delete_user_meta($user->ID, 'user_skills');
        }

        return true;
    }
}
