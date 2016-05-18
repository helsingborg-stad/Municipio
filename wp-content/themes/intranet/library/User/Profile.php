<?php

namespace Intranet\User;

class Profile
{
    public function __construct()
    {
        add_action('init', array($this, 'changeUrlRewrite'));
        add_action('template_redirect', array($this, 'accessControl'), 5);

        add_filter('wp_title', array($this, 'setProfileTitle'), 11, 3);
    }

    /**
     * Only show author/profile page to logged in users
     * @return void
     */
    public function accessControl()
    {
        global $wp_query;

        if (is_author() && !is_user_logged_in()) {
            $wp_query->set_404();
        }
    }

    /**
     * Change the url base for the author pages to /user
     * @return void
     */
    public function changeUrlRewrite()
    {
        global $wp_rewrite;

        if ($wp_rewrite->author_base != __('user', 'municipio-intranet')) {
            $wp_rewrite->author_base = __('user', 'municipio-intranet');
            $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';

            flush_rewrite_rules();
        }
    }

    /**
     * Set the page title for author page
     * @param string $title       The original title
     * @param string $sep         The separator character
     * @param string $seplocation The separator location
     * @return  string [<description>]
     */
    public function setProfileTitle($title, $sep, $seplocation)
    {
        if (is_author()) {
            $title = get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name');

            switch ($seplocation) {
                case 'right':
                    $title .= ' ' . $sep . ' ';
                    break;

                case 'left':
                    $title = ' ' . $sep . ' ' . $title;
            }
        }

        return $title;
    }
}
