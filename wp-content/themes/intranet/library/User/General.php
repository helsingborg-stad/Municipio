<?php

namespace Intranet\User;

class General
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'protectWpAdmin'));
    }

    public function protectWpAdmin()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (!current_user_can('edit_posts')) {
            wp_redirect(home_url());
        }
    }

    /**
     * Search users
     * @param  string $keyword Search keyword
     * @return array           Matching users
     */
    public static function searchUsers($keyword)
    {
        $userSearch = new \WP_User_Query(array(
            'search' => '*' . $keyword . '*',
        ));

        $userMetaSearch = new \WP_User_Query(array(
            //'search' => $keyword,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'first_name',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'last_name',
                    'value' => $keyword,
                    'compare' => 'LIKE'
                )
            )
        ));

        $users = array();
        foreach ($userSearch->get_results() as $user) {
            $users[$user->ID] = $user->data;
        }

        foreach ($userMetaSearch->get_results() as $user) {
            if (array_key_exists($user->ID, $users)) {
                continue;
            }

            $users[$user->ID] = $user->data;
        }

        return $users;
    }
}
