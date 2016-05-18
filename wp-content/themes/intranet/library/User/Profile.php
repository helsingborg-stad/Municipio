<?php

namespace Intranet\User;

class Profile
{
    public function __construct()
    {
        add_action('init', array($this, 'changeUrlRewrite'));
    }

    public function changeUrlRewrite()
    {
        global $wp_rewrite;
        $wp_rewrite->author_base = 'user';
        $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
    }
}
