<?php

namespace Intranet\User;

class RecursiveRegistration
{
    protected $defaultRole = 'subscriber';

    public function __construct()
    {
        // Add new user to all sites
        add_action('wpmu_activate_user', array($this, 'addDefaultRole'), 10, 1);
        add_action('wpmu_new_user', array($this, 'addDefaultRole'), 10, 1);
        add_action('user_register', array($this, 'addDefaultRole'), 10, 1);

        // Add existing users to new or activated sites
        add_action('wpmu_new_blog', array($this, 'addUsersToNewBlog'));
        add_action('wpmu_activate_blog', array($this, 'activateBlogUser'), 10, 2);
    }

    /**
     * Adds all users to the blog when
     * @param integer $blogId The newly created blog's ID
     */
    public function addUsersToNewBlog($blogId)
    {
        global $wpdb;
        $users = $wpdb->get_results("SELECT ID FROM $wpdb->users");

        foreach ($users as $user) {
            $this->addDefaultRole($user->ID, $blogId);
        }

        return true;
    }

    /**
     * Add user role when activated
     * @param  integer $blogId Blog id
     * @param  integer $userId User id
     * @return boolean
     */
    public function activateBlogUser($blogId, $userId)
    {
        return $this->addDefaultRole($userId, $blogId);
    }

    /**
     * Adds the specified userid to a specified or all blogs
     * @param integer $userId User id to add
     * @param integer $blogId Specific blog_id (leave null for all)
     */
    public function addDefaultRole($userId, $blogId = null)
    {
        // Single
        if ($blogId) {
            if (is_user_member_of_blog($userId, $blogId)) {
                return false;
            }

            add_user_to_blog($blogId, $userId, $this->defaultRole);
            return true;
        }

        // Multiple
        $sites = \Intranet\Helper\Multisite::getSitesList(true);

        foreach ($sites as $site) {
            if (is_user_member_of_blog($userId, $site['blog_id'])) {
                continue;
            }

            add_user_to_blog($site['blog_id'], $userId, $this->defaultRole);
        }

        return true;
    }
}
