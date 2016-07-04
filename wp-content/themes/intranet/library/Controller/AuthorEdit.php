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
            $this->removeProfileImage($user->ID);
            return wp_redirect($_SERVER['HTTP_REFERER']);
        }

        if (isset($_POST['image_uploader_file'][0]) && !empty($_POST['image_uploader_file'][0])) {
            $this->uploadProfileImage($_POST['image_uploader_file'][0], $user);
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
        update_user_meta($user->ID, 'user_department', sanitize_text_field($_POST['user_department']));
        update_user_meta($user->ID, 'user_about', implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['user_about']))));
        update_user_meta($user->ID, 'user_target_groups', isset($_POST['user_target_groups']) ? array_map('sanitize_text_field', $_POST['user_target_groups']) : array());
        update_user_meta($user->ID, 'user_color_scheme', isset($_POST['color_scheme']) ? sanitize_text_field($_POST['color_scheme']) : 'purple');

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

    /**
     * Uploads user profile image
     * @param  string $imageDataUri The image data uri
     * @param  object $user         User object
     * @return array                Profile image url
     */
    public function uploadProfileImage($imageDataUri, $user)
    {
        global $current_site;

        // Decode the imageDataUri
        $imageDataUri = str_replace(' ', '+', $imageDataUri);
        $imageDataUri = explode(',', $imageDataUri);
        $decodedImage = base64_decode($imageDataUri[1]);

        switch_to_blog($current_site->blog_id);

        $uploadDir = wp_upload_dir();
        $uploadDirUrl = $uploadDir['baseurl'];
        $uploadDir = $uploadDir['basedir'];
        $uploadDir = $uploadDir . '/profile-images';

        $fileType = preg_match_all('/data:image\/(.*);/', $imageDataUri[0], $matches);
        if (!isset($matches[1][0])) {
            return;
        }

        $fileType = $matches[1][0];

        switch ($fileType) {
            case 'jpeg':
                $fileType = 'jpg';
                break;

            default:
                $fileType = $fileType;
                break;
        }

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filePathWithName = $uploadDir . '/' . $user->data->user_login . '-' . uniqid() . '.' . $fileType;

        file_put_contents($filePathWithName, $decodedImage);
        restore_current_blog();

        $croppedImagePath = $this->cropProfileImage($filePathWithName, 250, 250);
        $profileImageUrl = $this->getProfileImageUrlFromPath($croppedImagePath);

        $this->removeProfileImage($user->ID);

        update_user_meta($user->ID, 'user_profile_picture', $profileImageUrl);

        return true;
    }

    /**
     * Resize/crop profile image to given size
     * @param  string  $path      Path to original image
     * @param  integer  $width    Width in pixels
     * @param  integer  $height   Height in pixels
     * @param  boolean $crop      Crop or just resize? true to crop
     * @return string             The cropped image's path
     */
    public function cropProfileImage($path, $width, $height, $crop = true)
    {
        $image = wp_get_image_editor($path);

        if (is_wp_error($image)) {
            return;
        }

        $image->set_quality(80);
        $image->resize($width, $height, $crop);
        $image->save($path);

        return $path;
    }

    /**
     * Rewrite profile image path to url
     * @param  string $path   The path
     * @return string         The url
     */
    public function getProfileImageUrlFromPath($path)
    {
        $path = explode('wp-content/', $path)[1];
        $url = content_url($path);

        return $url;
    }

    /**
     * Rewrite profile image to path
     * @param  string $url The path
     * @return string      The url
     */
    public function getProfileImagePathFromUrl($url)
    {
        $url = explode('wp-content/', $url)[1];
        $path = WP_CONTENT_DIR . '/' . $url;

        return $path;
    }

    /**
     * Removes a user's profile iamge
     * @param  integer $userId The user's id
     * @return boolean
     */
    public function removeProfileImage($userId)
    {
        $imageUrl = get_user_meta($userId, 'user_profile_picture', true);

        if (empty($imageUrl)) {
            return true;
        }

        $imagePath = $this->getProfileImagePathFromUrl($imageUrl);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        delete_user_meta($userId, 'user_profile_picture', $imageUrl);

        return true;
    }
}
