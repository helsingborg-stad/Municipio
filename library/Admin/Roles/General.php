<?php

namespace Municipio\Admin\Roles;

class General
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'removeUnusedRoles'));
    }

    public function removeUnusedRoles()
    {
        remove_role('author');
        remove_role('contributor');
    }
}
