<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class LogUserLoginTime implements Hookable
{
    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        // Track time
        $this->wpService->addAction('set_logged_in_cookie', array($this, 'setTimeStamp'), 5, 4);

        // Columns
        $this->wpService->addFilter('manage_users_columns', [$this, 'addColumn']);
        $this->wpService->addFilter('manage_users_custom_column', [$this, 'addColumnData'], 10, 3);

        // Sorting
        $this->wpService->addFilter('manage_users_sortable_columns', [$this, 'addSortableColumn']);
        $this->wpService->addAction('pre_get_users', [$this, 'performColumnSort']);
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
     * Add column
     *
     * @param array $columns
     *
     * @return array
     */
    public function addColumn($columns)
    {
        if (!is_array($columns)) {
            return $columns;
        }
        $columns['last_login'] = $this->wpService->__('Last Login', 'municipio');
        return $columns;
    }

    /**
     * Add column data
     *
     * @param string $output
     * @param string $columnId
     * @param int $userId
     *
     * @return string
     */
    public function addColumnData($output, $columnId, $userId)
    {
        if ($columnId == 'last_login') {
            $lastLogin = $this->wpService->getUserMeta($userId, 'last_login', true);
            $output    = $lastLogin ? date('Y-m-d H:i:s', $lastLogin) : '-';
        }
        return $output;
    }

    /**
     * Add sortable column
     *
     * @param array $columns
     *
     * @return array
     */
    public function addSortableColumn($columns)
    {
        return $this->wpService->wpParseArgs(array(
        'last_login' => 'last_login'
        ), $columns);
    }

    /**
     * Perform column sort
     *
     * @param object $query
     *
     * @return object
     */
    public function performColumnSort($query)
    {
        if (!$this->wpService->isAdmin()) {
            return $query;
        }

        if (function_exists('get_current_screen')) {
            $screen = $this->wpService->getCurrentScreen();
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
