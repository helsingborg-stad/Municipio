<?php

namespace Municipio\Admin\Roles;

class General
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'removeUnusedRoles'));
        add_action('set_current_user', array($this, 'hideAdminBarForUsersWhoCantCreatePosts'));
    }

    public function hideAdminBarForUsersWhoCantEditPosts()
    {
        if (current_user_can('edit_posts')) {
            return;
        }

        show_admin_bar(false);
    }

    public function removeUnusedRoles()
    {
        remove_role('author');
        remove_role('contributor');
    }
}
