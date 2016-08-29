<?php

namespace Municipio\Admin\Roles;

class General
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'removeUnusedRoles'));
        add_action('admin_init', array($this, 'addMissingRoles'));

        add_action('set_current_user', array($this, 'hideAdminBarForUsersWhoCantEditPosts'));
    }

    public function hideAdminBarForUsersWhoCantEditPosts()
    {
        if (current_user_can('edit_posts')) {
            return;
        }

        show_admin_bar(false);
    }

    /**
     * Adds back missing author role
     */
    public function addMissingRoles()
    {
        if (!get_role('author')) {
            add_role(
                'author',
                'Author',
                array(
                    'upload_files'           => true,
                    'edit_posts'             => true,
                    'edit_published_posts'   => true,
                    'publish_posts'          => true,
                    'read'                   => true,
                    'level_2'                => true,
                    'level_1'                => true,
                    'level_0'                => true,
                    'delete_posts'           => true,
                    'delete_published_posts' => true
                )
            );

            delete_option('_author_role_bkp');
        }
    }

    /**
     * Remove unwanted roles
     * @return void
     */
    public function removeUnusedRoles()
    {
        $removeRoles = array(
            'contributor'
        );

        foreach ($removeRoles as $role) {
            if (!get_role($role)) {
                continue;
            }

            update_option('_' . $role . '_role_bkp', get_role('author'));
            remove_role($role);
        }
    }
}
