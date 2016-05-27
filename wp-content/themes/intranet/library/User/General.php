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
        if (!current_user_can('edit_posts')) {
            wp_redirect(home_url());
        }
    }
}
