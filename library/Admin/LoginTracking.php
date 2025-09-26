<?php

namespace Municipio\Admin;

class LoginTracking
{
    public function __construct()
    {
        //Create login timestamp
        add_action('set_logged_in_cookie', array($this, 'setTimeStamp'), 5, 4);

        //Add timestamp column
        add_filter('manage_users_columns', array($this, 'addColumn'));
        add_filter('manage_users_custom_column', array($this, 'addColumnData'), 10, 3);

        //Add sorting ability
        add_filter('manage_users_sortable_columns', array($this, 'addSortableColumn'));
        add_action('pre_get_users', array($this, 'performColumnSort'));
    }

    /**
     * Set the login timestamp
     * @param string $loggedInCookie
     * @param int $expire
     * @param int $expiration
     * @param int $userId
     * @return void
     */
    public function setTimeStamp(string $loggedInCookie, int $expire, int $expiration, int $userId)
    {
        update_user_meta($userId, 'last_login', time());
    }

    /**
     * Add the column to the user list
     * @param array $columns
     * @return array
     */
    public function addColumn($columns)
    {
        if (!is_array($columns)) {
            return $columns;
        }
        $columns['last_login'] = __('Last Login', 'municipio');
        return $columns;
    }

    /**
     * Add data to the column
     * @param string $output
     * @param string $columnId
     * @param int $userId
     * @return string
     */
    public function addColumnData($output, $columnId, $userId)
    {

        if ($columnId == 'last_login') {
            $lastLogin = get_user_meta($userId, 'last_login', true);
            $output    = $lastLogin ? date('Y-m-d H:i:s', $lastLogin) : '-';
        }

        return $output;
    }

    /**
     * Make the column sortable
     * @param array $columns
     * @return array
     */
    public function addSortableColumn($columns)
    {
        return wp_parse_args(array(
         'last_login' => 'last_login'
        ), $columns);
    }

    /**
     * Perform the actual sorting of the column
     * 
     * @param \WP_User_Query $query
     * @return \WP_User_Query
     * 
     * @see https://developer.wordpress.org/reference/classes/wp_user_query/__construct/
     */
    public function performColumnSort($query)
    {

        if (!is_admin()) {
            return $query;
        }

        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if (isset($screen->id) && $screen->id !== 'users') {
                return $query;
            }
        }

        if (isset($_GET['orderby']) && $_GET['orderby'] == 'last_login') {
            $query->query_vars['meta_key'] = 'last_login';
            $query->query_vars['orderby']  = 'meta_value';
        }

        return $query;
    }
}
