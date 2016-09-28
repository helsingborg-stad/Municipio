<?php

namespace Intranet\User;

class Data
{
    // key => template
    public static $requiredUserData = array(
        'user_email' => 'user_email'
    );

    public static $requiredMetaFields = array(
        'user_administration_unit' => 'user_department',
        'user_department'          => 'user_department',
        'user_work_title'          => 'user_work_title',
        'first_name'               => 'name',
        'last_name'                => 'name'
    );

    public static $suggestedMetaFields = array(
        'user_phone'                        => 'user_phone',
        'user_skills'                       => 'user_skills',
        'user_responsibilities'             => 'user_responsibilities',
        'user_about'                        => 'user_about',
        'user_visiting_address[workplace]'  => 'user_visiting_address',
        'user_visiting_address[street]'     => 'user_visiting_address',
        'user_visiting_address[city]'       => 'user_visiting_address'
    );

    public function __construct()
    {
        add_action('wp', array($this, 'saveMissingDataForm'));
        add_action('wp_ajax_sync_facebook_profile', array($this, 'syncFacebookDetails'));

        add_action('MunicipioIntranet/save_profile_settings', array($this, 'saveProfileSettings'));
    }

    /**
     * Saves missing data from the user startup guide
     * @return void
     */
    public function saveMissingDataForm()
    {
        if (!isset($_POST['_wpnonce']) || !isset($_POST['user_missing_data'])) {
            return;
        }

        $user = wp_get_current_user();

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'user_missing_data_' . $user->ID)) {
            return;
        }

        unset($_POST['_wpnonce']);
        unset($_POST['_wp_http_referer']);
        unset($_POST['user_missing_data']);
        unset($_POST['active-section']);

        foreach ($_POST as $key => $value) {
            switch ($key) {
                case 'user_email':
                    $this->updateUserData($key, $value);
                    break;

                case 'image_uploader_file':
                    $this->uploadProfileImage($key, $value);
                    break;

                case 'user_phone':
                    $this->updateUserPhone($key, $value);
                    break;

                default:
                    $this->updateUserMeta($key, $value);
                    break;
            }
        }

        $referer = $_SERVER['HTTP_REFERER'];
        wp_redirect($referer);
        exit;
    }

    /**
     * Formats and updates user phone
     * @param  string $key   Meta key
     * @param  string $value Phone number (unformatted)
     * @return void
     */
    public function updateUserPhone($key, $value)
    {
        $phone = '';

        if (!empty($value)) {
            $phone = \Intranet\Helper\DataCleaner::phoneNumber($value);
        }

        update_user_meta(get_current_user_id(), $key, $phone);
    }

    /**
     * Upload profile image
     * @param  string $key   Array key (field)
     * @param  mixed  $value Array value
     * @return void
     */
    public function uploadProfileImage($key, $value)
    {
        if (!isset($value[0]) || empty($value[0])) {
            return;
        }

        $profileImage = new \Intranet\User\ProfileImage();
        $profileImage->uploadProfileImage($value[0], wp_get_current_user());
    }

    /**
     * Updates user data table
     * @param  string $key   Key
     * @param  string $value Value
     * @return void
     */
    public function updateUserData($key, $value)
    {
        $data = array();
        $data[$key] = $value;

        if (count($data) === 0) {
            return;
        }

        $data['ID'] = get_current_user_id();

        wp_update_user($data);
    }

    /**
     * Updates user meta
     * @param  string $key   Key
     * @param  string $value Value
     * @return void
     */
    public function updateUserMeta($key, $value)
    {
        update_user_meta(get_current_user_id(), $key, $value);
    }

    /**
     * Check if user is missing required user data
     * @param  integer $userId The users's id
     * @return array           List of missing fields (empty if none missing)
     */
    public static function missingRequiredUserData($userId = null)
    {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $fields = array();
        $userData = get_userdata($userId)->data;

        foreach (self::$requiredUserData as $field => $template) {
            if (!empty($userData->$field)) {
                continue;
            }

            $fields[$field] = $template;
        }

        $fields = array_unique($fields);

        return $fields;
    }

    /**
     * Check if user is missing required user meta fields
     * @param  integer $userId The users's id
     * @return array           List of missing fields (empty if none missing)
     */
    public static function missingRequiredFields($userId = null)
    {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $fields = array();

        foreach (self::$requiredMetaFields as $field => $template) {
            // Check if field has array key
            preg_match_all('/\\[([a-z|A-Z|0-9|_|-]+)\]/i', $field, $matches);

            // If field has array key, remove the keys from the $field variable
            $matches = $matches[1];
            if (!empty($matches)) {
                $field = preg_replace('/\\[([a-z|A-Z|0-9|_|-]+)\]/i', '', $field);
            }

            // Get the "base" meta value
            $metaValue = get_the_author_meta($field, $userId);

            // Get array key meta value (if key exist in $matches)
            if (!empty($matches)) {
                foreach ($matches as $value) {
                    $metaValue = $metaValue[$value];
                }
            }

            // Return if non-empty value
            if (!empty($metaValue)) {
                continue;
            }

            // Add to array of empty/missing fields
            $fields[$field] = $template;
        }

        $fields = array_unique($fields);

        return $fields;
    }

    /**
     * Check if user is missing suggested user meta fields
     * @param  integer $userId The users's id
     * @return array           List of missing fields (empty if none missing)
     */
    public static function missingSuggestedFields($userId = null)
    {
        if (is_null($userId)) {
            $userId = get_current_user_id();
        }

        $fields = array();

        foreach (self::$suggestedMetaFields as $field => $template) {
            // Check if field has array key
            preg_match_all('/\\[([a-z|A-Z|0-9|_|-]+)\]/i', $field, $matches);

            // If field has array key, remove the keys from the $field variable
            $matches = $matches[1];
            if (!empty($matches)) {
                $field = preg_replace('/\\[([a-z|A-Z|0-9|_|-]+)\]/i', '', $field);
            }

            // Get the "base" meta value
            $metaValue = get_the_author_meta($field, $userId);

            // Get array key meta value (if key exist in $matches)
            if (!empty($matches)) {
                foreach ($matches as $value) {
                    $metaValue = isset($metaValue[$value]) ? $metaValue[$value] : null;
                }
            }

            // Return if non-empty value
            if (!empty($metaValue)) {
                continue;
            }

            // Add to array of empty/missing fields
            $fields[$field] = $template;
        }

        $fields = array_unique($fields);

        return $fields;
    }

    /**
     * Save profile settings form
     * @return bool
     */
    public function saveProfileSettings()
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
        update_user_meta($user->ID, 'user_hometown', isset($_POST['user_hometown']) ? $_POST['user_hometown'] : '');
        update_user_meta($user->ID, 'user_administration_unit', $_POST['user_administration_unit']);
        update_user_meta($user->ID, 'user_department', $_POST['user_department']);

        update_user_meta($user->ID, 'user_facebook_url', $_POST['user_facebook_url']);
        update_user_meta($user->ID, 'user_linkedin_url', $_POST['user_linkedin_url']);
        update_user_meta($user->ID, 'user_instagram_username', $_POST['user_instagram_username']);
        update_user_meta($user->ID, 'user_twitter_username', $_POST['user_twitter_username']);

        update_user_meta($user->ID, 'user_about', implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['user_about']))));
        update_user_meta($user->ID, 'user_target_groups', isset($_POST['user_target_groups']) ? array_map('sanitize_text_field', $_POST['user_target_groups']) : array());
        update_user_meta($user->ID, 'user_color_scheme', isset($_POST['color_scheme']) ? $_POST['color_scheme'] : 'purple');

        if (!empty($_POST['user_birthday']['year']) && !empty($_POST['user_birthday']['month']) && !empty($_POST['user_birthday']['day'])) {
            update_user_meta($user->ID, 'user_birthday', $_POST['user_birthday']);
        } else {
            delete_user_meta($user->ID, 'user_birthday');
        }

        if (isset($_POST['user_hide_birthday'])) {
            update_user_meta($user->ID, 'user_hide_birthday', true);
        } else {
            delete_user_meta($user->ID, 'user_hide_birthday');
        }

        // Visiting address components
        $user_visiting_address = array(
            'workplace' => isset($_POST['user_visiting_address']['workplace']) ? $_POST['user_visiting_address']['workplace'] : '',
            'street'    => isset($_POST['user_visiting_address']['street']) ? $_POST['user_visiting_address']['street'] : '',
            'city'      => isset($_POST['user_visiting_address']['city']) ? $_POST['user_visiting_address']['city'] : ''
        );

        update_user_meta($user->ID, 'user_visiting_address', $user_visiting_address);

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
     * Ajax callback for syncing Facebook information to profile
     * @return void
     */
    public function syncFacebookDetails()
    {
        // Get the details from post
        $details = json_decode(json_encode($_POST['details']));
        $picture = json_decode(json_encode($_POST['picture']));

        // Update birthday
        if (isset($details->birthday) && !empty($details->birthday)) {
            $details->birthday = array(
                'year' => date('Y', strtotime($details->birthday)),
                'month' => ltrim(date('m', strtotime($details->birthday)), 0),
                'day' => ltrim(date('d', strtotime($details->birthday)), 0)
            );

            update_user_meta(get_current_user_id(), 'user_birthday', $details->birthday);
        }

        // Update location
        if (isset($details->location) && !empty($details->location)) {
            $details->location = explode(',', $details->location->name)[0];
            update_user_meta(get_current_user_id(), 'user_hometown', $details->location);
        }

        echo "1";
        wp_die();
    }
}
