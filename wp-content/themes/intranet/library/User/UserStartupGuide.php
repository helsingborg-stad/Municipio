<?php

namespace Intranet\User;

class UserStartupGuide
{
    public static $requiredUserData = array(
        'user_email'
    );

    public static $requiredMetaFields = array(

    );

    public static $suggestedMetaFields = array(
        'user_skills',
        'user_responsibility'
    );

    public function __construct()
    {
        /*
        add_action('init', array($this, 'urlRewrite'));
        add_filter('template_include', array($this, 'template'), 10);
        */

        //add_action('wp', array($this, 'initStartupGuide'));
    }

    /**
     * Force redirect to startup guide if user is missing required fields
     * @return void
     */
    public function initStartupGuide()
    {
        if (!is_user_logged_in()) {
            return;
        }

        global $wp_query;

        if (isset($wp_query->query['user_startup_guide']) && $wp_query->query['user_startup_guide'] && $wp_query->query['user_startup_guide'] == 'true') {
            return;
        }

        global $missingUserData;
        $missingUserData = array(
            'data' => self::missingRequiredUserData(),
            'meta' => self::missingRequiredFields()
        );

        if (empty($missingUserData['data']) && empty($missingUserData['meta'])) {
            return;
        }
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

        foreach (self::$requiredUserData as $field) {
            if (!empty($userData->$field)) {
                continue;
            }

            $fields[] = $field;
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

        foreach (self::$suggestedMetaFields as $field) {
            if (!empty(get_the_author_meta($field, $userId))) {
                continue;
            }

            $fields[] = $field;
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

        foreach (self::$suggestedMetaFields as $field) {
            if (!empty(get_the_author_meta($field, $userId))) {
                continue;
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Adds rewrite rules for the user startup guide
     * @return void
     */
    public function urlRewrite()
    {
        add_rewrite_rule('^user-startup-guide', 'index.php?user_startup_guide=true', 'top');
        add_rewrite_tag('%user_startup_guide%', 'true');

        flush_rewrite_rules();
    }

    /**
     * Use correct template for the user startup guide
     * @param  string $template Default template
     * @return string           Template to use
     */
    public function template($template)
    {
        global $wp_query;

        if (!isset($wp_query->query['user_startup_guide']) || !$wp_query->query['user_startup_guide'] || $wp_query->query['user_startup_guide'] == 'false') {
            return $template;
        }

        if (!is_user_logged_in()) {
            $wp_query->set404();
            return get_404_template();
        }

        $template = \Municipio\Helper\Template::locateTemplate('user-startup-guide');
        return $template;
    }
}
