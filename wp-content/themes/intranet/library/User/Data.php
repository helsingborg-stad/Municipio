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
        'user_department'          => 'user_department'
    );

    public static $suggestedMetaFields = array(
        'user_phone'            => 'user_phone',
        'user_skills'           => 'user_skills',
        'user_responsibilities' => 'user_responsibilities'
    );

    public function __construct()
    {
        add_action('wp', array($this, 'saveMissingDataForm'));
    }

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

                default:
                    $this->updateUserMeta($key, $value);
                    break;
            }
        }

        $referer = $_SERVER['HTTP_REFERER'];
        wp_redirect($referer);
        exit;
    }

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
            if (!empty(get_the_author_meta($field, $userId))) {
                continue;
            }

            $fields[$field] = $template;
        }

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
            if (!empty(get_the_author_meta($field, $userId))) {
                continue;
            }

            $fields[$field] = $template;
        }

        return $fields;
    }
}
