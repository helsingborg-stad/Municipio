<?php

namespace Intranet\User;

class Profile
{
    protected $urlBase = 'user';

    public function __construct()
    {
        $this->urlBase = __('user', 'municipio-intranet');

        // View profile
        add_action('init', array($this, 'profileUrlRewrite'));
        add_action('template_redirect', array($this, 'accessControl'), 5);
        add_filter('wp_title', array($this, 'setProfileTitle'), 11, 3);

        // Edit profile
        add_action('init', array($this, 'editProfileUrlRewrite'));
        add_filter('template_include', array($this, 'editProfileTemplate'), 10);
    }

    public function editProfileTemplate($template)
    {
        global $wp_query;

        // Bail if not on edit page
        if (!isset($wp_query->query['author_name']) || empty($wp_query->query['author_name']) || !isset($wp_query->query['editprofile']) || !$wp_query->query['editprofile'] || $wp_query->query['editprofile'] == 'false') {
            return $template;
        }

        $template = \Municipio\Helper\Template::locateTemplate('author-edit');
        return $template;
    }

    /**
     * Only show author/profile page to logged in users
     * @return void
     */
    public function accessControl()
    {
        global $wp_query;
        $currentUser = wp_get_current_user();

        if (is_author() && !is_user_logged_in()) {
            $wp_query->set_404();
        }

        if (isset($wp_query->query['editprofile']) && $wp_query->query['editprofile'] && (!is_super_admin() && $currentUser->user_login != $wp_query->query['author_name'])) {
            $wp_query->set_404();
        }
    }

    /**
     * Change the url base for the author pages to /user
     * @return void
     */
    public function profileUrlRewrite()
    {
        global $wp_rewrite;

        if ($wp_rewrite->author_base != $this->urlBase) {
            $wp_rewrite->author_base = $this->urlBase;
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

    /**
     * Adds edit page
     * @return void
     */
    public function editProfileUrlRewrite()
    {
        add_rewrite_rule('^' . $this->urlBase .'\/([a-zA-Z0-9_-]+)\/edit', 'index.php?author_name=$matches[1]&editprofile=true', 'top');
        add_rewrite_tag('%editprofile%', 'true');

        flush_rewrite_rules();
    }
}
